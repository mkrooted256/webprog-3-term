import pandas as pd
import requests
import os
import boto3
import uuid

def normalise_path(rel):
    return os.path.join(os.path.dirname(__file__), rel)

def tidy_vhi_up(provinces: pd.DataFrame):
    base_filename = normalise_path("cache/vhi_")
    for index, row in provinces.iterrows():
        with open(base_filename + str(row.provinceID) + ".csv", "rb+") as f:
            content = f.read()
            head = content[:1000]
            tail = content[-100:]

            a = head.rfind(b">") + 1
            b = tail.find(b"<")

            prid_pos = head.find(b"provinceID")  # label is incorrect
            # old_head_len = head.__len__()
            # head[a:prid_pos] + head[prid_pos + 10:head.rfind(b"I")]  # because label "VHI" is last
            content = content[a:prid_pos] + content[prid_pos + 10:-100 + b]

            print("File {}: head = {}, tail = {}".format(row.provinceID, a, -100 + b))
            f.seek(0, os.SEEK_SET)
            f.write(content)
            f.truncate()


def redownload_vhi(provinces: pd.DataFrame):
    url = "https://www.star.nesdis.noaa.gov/smcd/emb/vci/VH/get_provinceData.php?country=UKR&provinceID={}&year1=1981&year2=2019&type=Mean"
    base_filename = normalise_path("cache/vhi_")
    for index, row in provinces.iterrows():
        local_url = url.format(row.provinceID)
        print("Loading", row.provinceID, "(" + local_url + ")")
        r = requests.get(local_url, headers={"Accept": "text/plain"})
        with open(base_filename + str(row.provinceID) + ".csv", "wb") as out:
            out.write(r.content)


class AWSClient:
    def __init__(self):
        self.s3 = boto3.resource('s3')
        self.bucket = None

    def setup_new_bucket(self, frames, provinces) -> str:
        bucket_name = "ds-lab-{}".format(uuid.uuid4())
        print("Creating new bucket called {}...".format(bucket_name))
        self.s3.meta.client.create_bucket(Bucket=bucket_name)
        self.bucket = self.s3.Bucket(bucket_name)

        for index, row in provinces.iterrows():
            print("Uploading vhi_{}".format(row.provinceID))
            with open(normalise_path("temp.csv"), "w") as f:
                frames[row.provinceID].to_csv(f, index=False)
            self.bucket.upload_file(Filename="temp.csv", Key="vhi_{}.csv".format(row.provinceID))
        print("Bucket ready")
        return bucket_name

    def load_from_bucket(self, bucket_name, provinces):
        frames = {}
        print("Loading from bucket {}...".format(bucket_name))
        self.bucket = self.s3.Bucket(bucket_name)
        for index, row in provinces.iterrows():
            print("Downloading vhi_{}".format(row.provinceID))
            self.bucket.Object("vhi_{}.csv".format(row.provinceID)).download_file(normalise_path("temp.csv"))
            with open(normalise_path("temp.csv"), "r") as f:
                frames[row.provinceID] = pd.read_csv(f, sep="[, ]+", engine="python")
        print("Loaded.")
        return frames


class VHIProvider:
    class Province:
        def __init__(self, old_id: int, new_id: int, name: str):
            self.old_id = old_id
            self.new_id = new_id
            self.name = name

    @staticmethod
    def load_provinces():
        df = pd.read_csv(normalise_path('selected_provinces.csv'), header=1)
        return df[df['CountryCode'] == 'UKR'].filter(items=["provinceID", "province_name"])

    def __init__(self):
        self.raw_provinces = self.load_provinces()
        self.new_prid_rules = pd.read_csv(normalise_path('new_provinces.csv'), encoding="utf_8")
        self.frames = None

    def load_from_disk(self):
        base_filename = "cache/vhi_"
        frames = {}
        for index, row in self.raw_provinces.iterrows():
            print("loading vhi_{}".format(row.provinceID))
            frames[row.provinceID] = pd.read_csv(normalise_path(base_filename + str(row.provinceID) + ".csv"), sep="[, ]+",
                                                 engine="python")
        self.frames = frames

    def load_from_s3(self, bucket_name):
        awsclient = AWSClient()
        self.frames = awsclient.load_from_bucket(bucket_name, self.raw_provinces)

    def prid_to_new(self, old_prid):
        return self.new_prid_rules[self.new_prid_rules['provinceID_old'] == old_prid]['provinceID'].values[0]

    def prid_to_old(self, new_prid):
        return self.new_prid_rules[self.new_prid_rules['provinceID'] == new_prid]['provinceID_old'].values[0]

    def province_from_old(self, old_id):
        return self.Province(old_id, self.prid_to_new(old_id),
                             self.new_prid_rules[self.new_prid_rules['provinceID_old'] == old_id][
                                 'province_name'].values[0])

    def province_from_new(self, new_id):
        return self.Province(self.prid_to_old(new_id), new_id,
                             self.new_prid_rules[self.new_prid_rules['provinceID'] == new_id][
                                 'province_name'].values[0])

    def get_vhi(self, province: Province):
        return self.frames[province.old_id]


class VHIHandler:
    VHI_EXTREME_DRY = 1
    VHI_MEDIUM_DRY = 2
    VHI_STRESS = 3
    VHI_NORMAL = 4
    VHI_NICE = 5
    VHI_LEVELS = (0, 15, 35, 40, 60, 100)

    def __init__(self, df: pd.DataFrame):
        self.df = df

    def vhi_by_year(self, year: int):
        result = self.df[self.df['year'] == year]['VHI']
        return {
            "vhi": result,
            "min": result.min(),
            "max": result.max()
        }

    def vhi_by_level(self, level: int):
        result = self.df[self.df['VHI'] < self.VHI_LEVELS[level] & self.df['VHI'] > self.VHI_LEVELS[level - 1]]
        years = result['year'].drop_duplicates().values
        return {
            "vhi": result,
            "years": years
        }
