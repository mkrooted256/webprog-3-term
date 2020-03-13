from typing import List, Any, Tuple

from spyre import server
import load

# Load all Ukrainian provinces
p = load.VHIProvider.load_provinces()
# Transform df into the comfortable and iterable format
PROVINCES: List[Tuple[int, str]] = list(zip(p.provinceID, p.province_name))


class VHIapp(server.App):
    title = "Very EcoActive App"

    tabs = ['Table', 'Plot']

    inputs = [{
        "type": 'dropdown',
        "label": 'Series',
        "options": [
            {"label": "VCI", "value": "VCI"},
            {"label": "TCI", "value": "TCI"},
            {"label": "VHI", "value": "VHI"}],
        "value": 'VHI',
        "key": 'series',
        "action_id": "update_data"
    }, {
        "type": 'dropdown',
        "label": 'Province',
        "options": [{"label": name, "value": id} for (id, name) in PROVINCES],
        "value": 1,  # first province
        "key": 'pid',
        "action_id": "update_data"
    }, {
        "type": 'slider',
        "label": 'Year',
        "min": 1982, "max": 2019, "value": 1982,
        "key": 'year',
        "action_id": "update_data"
    }, {
        "type": 'slider',
        "label": 'First week',
        "min": 0, "max": 60, "value": 0,
        "key": 'week_low',
        "action_id": "update_data"
    }, {
        "type": 'slider',
        "label": 'Last week',
        "min": 0, "max": 60, "value": 0,
        "key": 'week_high',
        "action_id": "update_data"
    }]

    controls = [{
        "type": "hidden",
        "id": "update_data"
    }]

    outputs = [{
        "control_id": "update_data",
        "type": "html",
        "id": "simple_html_output",
        "tab": "Table"
    }, {
        "control_id": "update_data",
        "type": "table",
        "id": "series_table",
        "tab": "Table"
    }, {
        "control_id": "update_data",
        "type": "plot",
        "id": "plot",
        "tab": "Plot"
    }]

    def __init__(self) -> None:
        super().__init__()
        self.provider = load.VHIProvider()
        self.provider.load_from_disk()
        self.data_cache = None
        self.params_cache = None

    def getData(self, params):
        params.pop("output_id", None)    # caching layer
        if self.params_cache != params:  # caching layer
            series = params['series']
            year = int(params['year'])
            weeks = int(params['week_low']), int(params['week_high'])
            pid = int(params['pid'])
            if series == 'empty':
                series = 'VHI'
            df = self.provider.get_vhi(self.provider.province_from_old(pid))

            # caching layer
            self.data_cache = df[(df['year'] == year) &
                                 (weeks[0] <= df['week']) &
                                 (df['week'] <= weeks[1])][['year', 'week', series]]
            self.params_cache = params
        return self.data_cache

    def getPlot(self, params):
        df = self.getData(params).set_index('week').drop(['year'], axis=1)
        plt_obj = df.plot()
        plt_obj.set_ylabel(params['series'])
        plt_obj.set_title(
            "%s %s data for %s province" % (int(params['year']), params['series'], PROVINCES[int(params["pid"]) - 1][1])
        )
        fig = plt_obj.get_figure()
        return fig

    def getHTML(self, params):
        pid, pname, s = params["pid"], PROVINCES[int(params["pid"]) - 1][1], params["series"]
        html = "Here are your inputs: <table>" \
               "<tr><td>Series</td><td>%s</td></tr>" \
               "<tr><td>Province</td><td>%s (%s)</td></tr>" \
               "</table>" % (s, pname, pid)
        if int(params['week_low']) > int(params['week_high']):
            html = "<p class='error'>INVALID INPUT: 'First week' must be greater than 'Last week'</p>"
        return html

    def getCustomCSS(self):
        css = super().getCustomCSS()
        css += """
        table, tr, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 3px;
        }
        .error {
            color: red;
            border: 1px solid red;
            padding: 10px;
        }
        """
        return css


app = VHIapp()
app.launch()
