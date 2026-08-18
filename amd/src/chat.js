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

/**
 * Initializes the LearnWise chat iframe and launcher script.
 *
 * @module     block_learnwise/chat
 * @copyright  2026 LearnWise <help@learnwise.ai>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 *
 * @param {Object} config Setup config
 */
export const init = (config) => {
    const frame = document.getElementById(config.frameId);

    window.learnWiseSetup = {
        host: config.injectorHost,
        chatSrc: config.chatUrl,
        assistantId: config.assistantId,
        region: config.region || undefined,
        courseId: config.courseId ? String(config.courseId) : undefined,
        hideButton: true
    };

    frame.src = config.chatUrl;

    if (!document.getElementById('learnwise-chat-launcher')) {
        const script = document.createElement('script');
        script.id = 'learnwise-chat-launcher';
        script.src = `${config.injectorHost}/chat_launcher.js`;
        script.async = true;
        document.head.appendChild(script);
    }
};