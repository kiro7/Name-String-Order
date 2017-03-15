<?php
/**
 * Name Order
 *
 * @version    0.1 (2017-03-15 22:39:00 GMT)
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

class nameOrder {

  /**
   * Version
   * @var string
   */
  const VERSION = '0.1';

  #===================================================================

  /**
   * Reorders elements of a name: 'First Last'
   * Detects which name is surname (last) according to all upper-case
   * (WONG Janet)
   * and accordning a dictionary of given names.
   * LAST First -> First Last
   * Doe John   -> John Doe
   * @var string
   */
  private static function firstLast($str) {
    if (strpos($str, ' ') === false) {
      return self::mb_ucname($str);
    }
    $first = '';
    $last  = '';
    $arr = explode(' ', $str);
    foreach ($arr as $key => $val) {
      if (mb_strlen($val) > 1 && mb_convert_case($val, MB_CASE_UPPER, "UTF-8") == $val) {
        $last .= ' '.$val;
      }
      else {
        $first .= ' '.$val;
      }
    }
    $str = false;
    $first = trim($first);
    $last  = trim($last);
    #----
    if (empty($last)) {
      $str = $first;
      $first = '';
      $last  = '';
    }
    elseif (empty($first)) {
      $str = $last;
      $first = '';
      $last  = '';
    }
    #----
    if (!empty($str)) {
      $arr = explode(' ', $str);
      require __DIR__.'/dictionary-first-names.php';
      foreach ($arr as $key => $val) {
        # Last name usually is not one character
        if (mb_strlen($val) > 1 && !in_array(mb_convert_case($val, MB_CASE_LOWER, "UTF-8"), $dict)) {
          $last .= ' '.$val;
        }
        else {
          $first .= ' '.$val;
        }
      }
      $first = trim($first);
      $last  = trim($last);
    }
    #----
    if (empty($last)) {
      $str = $first;
      $first = '';
      $last  = '';
      $arr = explode(' ', $str);
      foreach ($arr as $val) {
        if (empty($first)) {
          $first = $val;
        }
        else {
          $last .= ' '.$val;
        }
      }
      $last = trim($last);
    }
    elseif (empty($first)) {
      $str = $last;
      $first = '';
      $last  = '';
      $arr = explode(' ', $str);
      foreach ($arr as $val) {
        if (empty($first)) {
          $first = $val;
        }
        else {
          $last .= ' '.$val;
        }
      }
      $last = trim($last);
    }
    #----
    if (preg_match('/\S+ová$/', $first)) { # Czech female last name
      $temp  = $last;
      $last  = $first;
      $first = $temp;
    }
    #----
    $str = trim($first.' '.$last);
    return self::mb_ucname($str);
  }

  #===================================================================

  /**
   * Handles hyphenated names.
   * @var string
   */
  private static function mb_ucname($str) {
    $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
    if (strpos($str, '-') !== false) {
      $str = implode('-', array_map('mb_ucfirst', explode('-', $str)));
    }
    return $str;
  }

  #===================================================================
}