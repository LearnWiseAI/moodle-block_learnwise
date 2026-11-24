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

use admin_setting_configtextarea;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Class setting_assistants
 *
 * @package    block_learnwise
 * @copyright  2026 LearnWise <help@learnwise.ai>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class setting_assistants extends admin_setting_configtextarea {
    /**
     * {@inheritdoc}
     */
    public function validate($data) {
        $errors = parent::validate($data);

        if ($errors !== true) {
            return $errors;
        }

        $lines = preg_split('/\r\n|\r|\n/', $data);

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $parts = explode('|', $line);

            if (
                count($parts) !== 2 ||
                    !preg_match('/^[a-zA-Z0-9_]+$/', trim($parts[1]))
            ) {
                return get_string('invalidassistants', 'block_learnwise');
            }
        }

        return true;
    }
}
