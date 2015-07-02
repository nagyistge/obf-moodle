<?php

defined('MOODLE_INTERNAL') or die();

require_once(__DIR__ . '/obfform.php');
require_once(__DIR__ . '/../renderer.php');

class obf_blacklist_form extends local_obf_form_base {
    private $blacklist;
    protected function definition() {
        global $OUTPUT;

        $mform = $this->_form;
        $this->blacklist = $this->_customdata['blacklist'];
        $user = $this->_customdata['user'];
        $client = new obf_client();
        $assertions = obf_assertion::get_assertions($client, null, $user->email);
        $unique_assertions = new obf_assertion_collection();
        $unique_assertions->add_collection($assertions);

        $this->render_badges($unique_assertions, $mform);

        $this->add_action_buttons();
    }

    private function render_badges(obf_assertion_collection $assertions, &$mform) {
        global $PAGE, $OUTPUT;

        $items = array();
        $renderer = $PAGE->get_renderer('local_obf');
        $size = local_obf_renderer::BADGE_IMAGE_SIZE_NORMAL;

        $mform->addElement('html', $OUTPUT->notification(get_string('blacklistdescription', 'local_obf'), 'notifymessage'));

        for ($i = 0; $i < count($assertions); $i++) {
            $assertion = $assertions->get_assertion($i);
            $badge = $assertion->get_badge();
            $html = $OUTPUT->box(local_obf_html::div($renderer->render_single_simple_assertion($assertion, true) ));
            $items[] = $mform->createElement('advcheckbox', 'blacklist['.$badge->get_id().']',
                    '', $html);
        }
        if (count($items) > 0) {
            $mform->addGroup($items, 'blacklist', '', array(' '), false);
        }

        $badgeids = $this->blacklist->get_blacklist();
        foreach ($badgeids as $badgeid) {
            $mform->setDefault('blacklist['.$badgeid.']', 1);
        }
    }

}
