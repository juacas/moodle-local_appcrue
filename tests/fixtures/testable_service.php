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

namespace local_appcrue;

/**
 * Lightweight test double to avoid request-dependent constructor logic.
 *
 * @package    local_appcrue
 * @copyright  2026
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_service extends appcrue_service {
    /** @var array Items returned by get_items(). */
    private array $items;

    /**
     * Constructor.
     *
     * @param array $items Items returned by get_items().
     */
    public function __construct(array $items) {
        $this->items = $items;
    }

    /**
     * Return fixture items.
     *
     * @return array
     */
    public function get_items(): array {
        return $this->items;
    }
}
