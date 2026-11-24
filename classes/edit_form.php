<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace block_learnwise;

use block_edit_form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/edit_form.php');

/**
 * Form for editing block_learnwise instances
 *
 * @package    block_learnwise
 * @copyright  2026 LearnWise <help@learnwise.ai>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edit_form extends block_edit_form {
    /**
     * Form fields specific to this type of block
     *
     * @param \MoodleQuickForm $mform
     */
    protected function specific_definition($mform) {
        global $OUTPUT;
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $options = self::get_assistant_options();
        if (empty($options)) {
            $notice = $OUTPUT->notification(get_string('emptynotice', 'block_learnwise'), 'warning');
            $mform->addElement('html', $notice);
        }
        $mform->addElement('select', 'config_assistantid', get_string('assistant_instances', 'block_learnwise'), $options);
        $mform->setType('config_assistantid', PARAM_ALPHANUMEXT);
        $mform->addRule('config_assistantid', get_string('required'), 'required', null, 'client');

        if (isset($this->block->config->assistantid)) {
            $mform->setDefault('config_assistantid', $this->block->config->assistantid);
        }
    }

    /**
     * Get assistant options
     * @return array
     */
    public static function get_assistant_options() {
        $options = [];
        $assistants = get_config('block_learnwise', 'assistants');
        if (!empty($assistants)) {
            $lines = explode("\n", $assistants);
            foreach ($lines as $line) {
                $line = trim($line);
                $bits = explode('|', $line, 2);
                if (count($bits) !== 2) {
                    continue;
                }
                $options[trim($bits[1])] = $bits[0];
            }
        }
        return $options;
    }
}
