<?php
/**
 * Name String Order
 *
 * @version    0.4 (2017-04-10 09:33:00 GMT)
 * @author     Peter Kahl <peter.kahl@colossalmind.com>
 * @copyright  2017 Peter Kahl
 * @license    Apache License, Version 2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      <http://www.apache.org/licenses/LICENSE-2.0>
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace peterkahl\nameStringOrder;

class nameStringOrder {

  /**
   * Version
   * @var string
   */
  const VERSION = '0.4';

  /**
   * First name
   * @var string
   */
  private $first;

  /**
   * Last name
   * @var string
   */
  private $last;

  #===================================================================

  public function __construct($unordered) {
    $this->first = '';
    $this->last  = '';
    $this->segment($unordered);
  }

  #===================================================================

  /**
   * Returns 'First'
   * @var string
   */
  public function getFirst() {
    return $this->mb_ucname($this->first);
  }

  #===================================================================

  /**
   * Returns 'Last'
   * @var string
   */
  public function getLast() {
    return $this->mb_ucname($this->last);
  }

  #===================================================================

  /**
   * Returns 'First Last'
   * @var string
   */
  public function getFirstLast() {
    return $this->mb_ucname(trim($this->first.' '.$this->last));
  }

  #===================================================================

  /**
   * Returns 'Last First'
   * @var string
   */
  public function getLastFirst() {
    return $this->mb_ucname(trim($this->last.' '.$this->first));
  }

  #===================================================================

  /**
   * Detects which name is surname (last) according to all upper-case
   * (WONG Janet) and which is given name (first)
   * and accordning a dictionary of given names.
   * @var string
   */
  private function segment($unordered) {
    if (empty($unordered)) {
      return;
    }
    if (strpos($unordered, ' ') === false) {
      # Let's assume that it's last name
      $this->last = $unordered;
      return;
    }
    $arr = explode(' ', $unordered);
    foreach ($arr as $key => $val) {
      if (mb_strlen($val) > 1 && mb_convert_case($val, MB_CASE_UPPER, "UTF-8") == $val) {
        $this->last .= ' '.$val;
      }
      else {
        $this->first .= ' '.$val;
      }
    }
    $unordered = false;
    $this->first = trim($this->first);
    $this->last  = trim($this->last);
    #----
    if (empty($this->last)) {
      $unordered = $this->first;
      $this->first = '';
      $this->last  = '';
    }
    elseif (empty($this->first)) {
      $unordered = $this->last;
      $this->first = '';
      $this->last  = '';
    }
    #----
    if (!empty($unordered)) {
      $arr = explode(' ', $unordered);
      require __DIR__.'/dictionary-first-names.php';
      foreach ($arr as $key => $val) {
        # Last name usually isn't one character
        if (mb_strlen($val) > 1 && !in_array(mb_convert_case($val, MB_CASE_LOWER, "UTF-8"), $dict)) {
          $this->last .= ' '.$val;
        }
        else {
          $this->first .= ' '.$val;
        }
      }
      $this->first = trim($this->first);
      $this->last  = trim($this->last);
    }
    #----
    if (empty($this->last)) {
      $unordered = $this->first;
      $this->first = '';
      $this->last  = '';
      $arr = explode(' ', $unordered);
      foreach ($arr as $val) {
        if (empty($this->first)) {
          $this->first = $val;
        }
        else {
          $this->last .= ' '.$val;
        }
      }
      $this->last = trim($this->last);
    }
    elseif (empty($this->first)) {
      $unordered = $this->last;
      $this->first = '';
      $this->last  = '';
      $arr = explode(' ', $unordered);
      foreach ($arr as $val) {
        if (empty($this->first)) {
          $this->first = $val;
        }
        else {
          $this->last .= ' '.$val;
        }
      }
      $this->last = trim($this->last);
    }
    #----
    if (preg_match('/\S+ová$/', $this->first)) { # Czech female last name
      $temp  = $this->last;
      $this->last  = $this->first;
      $this->first = $temp;
    }
  }

  #===================================================================

  /**
   * UC First (name)
   * Handles hyphenated names.
   * @var string
   */
  private function mb_ucname($str) {
    $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
    if (strpos($str, '-') !== false) {
      $str = implode('-', array_map('mb_ucfirst', explode('-', $str)));
    }
    return $str;
  }

  #===================================================================
}