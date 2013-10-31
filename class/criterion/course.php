<?php

global $CFG;

require_once($CFG->libdir . '/coursecatlib.php');
require_once($CFG->libdir . '/completionlib.php');
require_once(__DIR__ . '/criterionbase.php');
require_once(__DIR__ . '/criterion.php');

class obf_criterion_course extends obf_criterion_base {

    protected $courseid = -1;
    protected $grade = -1;
    protected $completedby = -1;
    protected $coursename = '';
    protected $criterion = null;

    public static function get_instance($id) {
        global $DB;

        $record = $DB->get_record('obf_criterion_courses', array('id' => $id));
        $obj = new self();

        return $obj->populate_from_record($record);
    }

    public static function get_criterion_courses(obf_criterion $criterion) {
        global $DB;

        $records = $DB->get_records('obf_criterion_courses',
                array('obf_criterion_id' => $criterion->get_id()));
        $ret = array();

        foreach ($records as $record) {
            $obj = new self();
            $ret[] = $obj->populate_from_record($record);
        }

        return $ret;
    }

    public function has_grade() {
        return (!empty($this->grade) && $this->grade > 0);
    }

    public function has_completion_date() {
        return (!empty($this->completedby) && $this->completedby > 0);
    }

    public function populate_from_record(\stdClass $record) {
        $this->set_id($record->id)
                ->set_criterionid($record->obf_criterion_id)
                ->set_courseid($record->courseid)
                ->set_grade($record->grade)
                ->set_completedby($record->completed_by);

        return $this;
    }

    public function get_criterion() {
        if (is_null($this->criterion)) {
            $this->criterion = obf_criterion::get_instance($this->criterionid);
        }

        return $this->criterion;
    }

    /**
     *
     * @global moodle_database $DB
     */
    public function save() {
        global $DB;

        $obj = new stdClass();
        $obj->obf_criterion_id = $this->criterionid;
        $obj->courseid = $this->courseid;
        $obj->grade = $this->has_grade() ? $this->grade : null;
        $obj->completed_by = $this->has_completion_date() ? $this->completedby : null;

        // Updating existing record
        if ($this->id > 0) {
            $obj->id = $this->id;
            $DB->update_record('obf_criterion_courses', $obj);
        }

        // Inserting a new record
        else {
            $id = $DB->insert_record('obf_criterion_courses', $obj);

            if (!$id) {
                return false;
            }

            $this->set_id($id);
        }

        return $this;
    }

    public function get_courseid() {
        return $this->courseid;
    }

    public function get_grade() {
        return $this->grade;
    }

    public function get_completedby() {
        return $this->completedby;
    }

    public function set_courseid($courseid) {
        $this->courseid = $courseid;
        return $this;
    }

    public function set_grade($grade) {
        $this->grade = $grade;
        return $this;
    }

    public function set_completedby($completedby) {
        $this->completedby = $completedby;
        return $this;
    }

    /**
     *
     * @global moodle_database $DB
     */
    public function get_coursename() {
        global $DB;

        if (empty($this->coursename)) {
            $this->coursename = $DB->get_field('course', 'fullname', array('id' => $this->courseid));
        }

        return $this->coursename;
    }

    public function get_text() {
        $html = html_writer::tag('strong', $this->get_coursename());

        if ($this->has_completion_date()) {
            $html .= ' ' . get_string('completedbycriterion', 'local_obf', userdate($this->completedby,
                    get_string('strftimedate')));
        }

        if ($this->has_grade()) {
            $html .= ' ' . get_string('gradecriterion', 'local_obf', $this->grade);
        }

        return $html;
    }

    public function get_text_for_single_course() {
        $html = get_string('toearnthisbadge', 'local_obf');

        if ($this->has_completion_date()) {
            $html .= ' ' . get_string('completedbycriterion', 'local_obf', userdate($this->completedby,
                    get_string('strftimedate')));
        }

        if ($this->has_grade()) {
            $html .= ' ' . get_string('gradecriterion', 'local_obf', $this->grade);
        }

        $html .= '.';

        return $html;
    }

    public function delete() {
        global $DB;

        $DB->delete_records('obf_criterion_courses', array('id' => $this->id));
        obf_criterion::delete_empty();
    }

    public static function delete_by_course(stdClass $course) {
        global $DB;

        // First delete criterion courses
        $DB->delete_records('obf_criterion_courses', array('courseid' => $course->id));

        // Then delete "empty" criteria (= criteria that don't have any related courses
        obf_criterion::delete_empty();
    }

}