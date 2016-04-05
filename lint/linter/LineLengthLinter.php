<?php
/*
 Copyright 2016-present Google Inc. All Rights Reserved.

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
 */


/**
 * Line length linter that can be configured to ignore lines containing certain regexes.
 */
final class LineLengthLinter extends ArcanistLinter {

  private $maxLineLength = 80;
  private $ignoreLineRegexes = array();

  public function getInfoName() {
    return pht('Regex Line Length Linter');
  }

  public function getInfoDescription() {
    return 'Enforces line length with options for ignoring lines containing certain regexes.';
  }

  public function getLinterPriority() {
    return 0.5;
  }

  public function getLinterConfigurationOptions() {
    $options = array(
      'max-line-length' => array(
        'type' => 'optional int',
        'help' => pht(
          'Adjust the maximum line length before a warning is raised. By '.
          'default, a warning is raised on lines exceeding 80 characters.'),
      ),
      'ignore-line-regexes' => array(
        'type' => 'optional list<regex>',
        'help' => pht(
          'Provide a list of regexes that allow the linter to ignore '.
          'certain lines.'),
      ),
    );

    return $options + parent::getLinterConfigurationOptions();
  }

  public function setMaxLineLength($new_length) {
    $this->maxLineLength = $new_length;
    return $this;
  }

  public function setIgnoreLineRegexes($new_regexes) {
    $this->ignoreLineRegexes = $new_regexes;
    return $this;
  }

  public function setLinterConfigurationValue($key, $value) {
    switch ($key) {
      case 'max-line-length':
        $this->setMaxLineLength($value);
        return;
      case 'ignore-line-regexes':
        $this->setIgnoreLineRegexes($value);
        return;
    }

    return parent::setLinterConfigurationValue($key, $value);
  }

  public function getLinterName() {
    return 'REGEXLINELENGTH';
  }

  public function getLinterConfigurationName() {
    return 'regex-line-length';
  }

  public function lintPath($path) {
    if (!strlen($this->getData($path))) {
      // If the file is empty, don't bother; particularly, don't require
      // the user to add a newline.
      return;
    }

    if ($this->didStopAllLinters()) {
      return;
    }

    $this->lintLineLength($path);
  }

  // TODO(featherless): Look into whether we can avoid having to specify the following two maps.
  public function getLintSeverityMap() {
    return array(
      1           => ArcanistLintSeverity::SEVERITY_WARNING
    );
  }

  public function getLintNameMap() {
    return array(
      1           => pht('Line Too Long')
    );
  }

  protected function lintLineLength($path) {
    $lines = explode("\n", $this->getData($path));

    $width = $this->maxLineLength;
    foreach ($lines as $line_idx => $line) {
      if (strlen($line) > $width) {
        $ignore = false;
        foreach ($this->ignoreLineRegexes as $regex) {
          if (preg_match($regex, $line)) {
            $ignore = true;
            break;
          }
        }
        if ($ignore) {
          continue;
        }
        $this->raiseLintAtLine(
          $line_idx + 1,
          1,
          1,
          pht(
            'This line is %s characters long, but the '.
            'convention is %s characters.',
            new PhutilNumber(strlen($line)),
            $width),
          $line);
      }
    }
  }
}
