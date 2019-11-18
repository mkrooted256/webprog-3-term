<?php

class User {
    protected $name_b;
    protected $name_p;
    protected $name_i;
    protected $yob;

    /**
     * User constructor.
     * @param string $name_p
     * @param string $name_i
     * @param string $name_b
     * @param integer $yob
     */
    public function __construct($name_p, $name_i, $name_b, $yob)
    {
        p( "New user: ", $name_p, $name_i, $name_b);
        $this->name_p = $name_p;
        $this->name_i = $name_i;
        $this->name_b = $name_b;
        $this->yob = $yob;
    }


    /**
     * @return string
     */
    public function getNameP()
    {
        return $this->name_p;
    }

    /**
     * @param string $name_p
     */
    public function setNameP($name_p)
    {
        $this->name_p = $name_p;
    }

    /**
     * @return string
     */
    public function getNameI()
    {
        return $this->name_i;
    }

    /**
     * @param string $name_i
     */
    public function setNameI($name_i)
    {
        $this->name_i = $name_i;
    }

    /**
     * @return string
     */
    public function getNameB()
    {
        return $this->name_b;
    }

    /**
     * @param string $name_b
     */
    public function setNameB($name_b)
    {
        $this->name_b = $name_b;
    }

    /**
     * @return integer
     */
    public function getYob()
    {
        return $this->yob;
    }

    /**
     * @param integer $yob
     */
    public function setYob($yob)
    {
        $this->yob = $yob;
    }


    public function getAge() {
        return 2019 - $this->yob;
    }

    public function getPIB() {
        return "{$this->name_p} {$this->name_i} {$this->name_b}";
    }
};

class Student extends User {
    private $faculty;
    private $cafedra;
    private $group;

    public function __construct($faculty, $cafedra, $group, $name_p, $name_i=null, $name_b=null, $yob=null)
    {
        if (is_a($name_p, "User")) {
            $yob = $name_p->yob;
            $name_b = $name_p->name_b;
            $name_i = $name_p->name_i;
            $name_p = $name_p->name_p;
        }

        p( "New student: ", $name_p, $name_i, $name_b, ";", $faculty, $cafedra, $group);
        User::__construct($name_p, $name_i, $name_p, $yob);
        $this->faculty = $faculty;
        $this->cafedra = $cafedra;
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getFaculty()
    {
        return $this->faculty;
    }

    /**
     * @param mixed $faculty
     */
    public function setFaculty($faculty)
    {
        $this->faculty = $faculty;
    }

    /**
     * @return mixed
     */
    public function getCafedra()
    {
        return $this->cafedra;
    }

    /**
     * @param mixed $cafedra
     */
    public function setCafedra($cafedra)
    {
        $this->cafedra = $cafedra;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }


    public function getApplicationYear() {
        return 2000 + intval($this->group[3]);
    }

    public function getSubgroupId() {
        return intval($this->group[4]);
    }
}

function p(...$args) {
    echo"<p>";

    foreach ($args as $arg) {
        echo $arg, " ";
    }

    echo "</p>";
}

function test() {
    $u = new User("User", "1", "The 1st", 1990);
    p( "PIB:", $u->getPIB());
    p( "Age:", $u->getAge());

    $s = new Student("F1", "C1", "AA-12", $u);
    p( "Application year:", $s->getApplicationYear());
    p( "Subgroup:", $s->getSubgroupId());
}

test();