<?php
/**
 * The MIT License (MIT)
 *
 * Webzash - Easy to use web based double entry accounting software
 *
 * Copyright (c) 2014 Prashant Shah <pshah.mumbai@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

App::uses('WebzashAppModel', 'Webzash.Model');

/**
 * Webzash Plugin Entry Model
 *
 * @package Webzash
 * @subpackage Webzash.Model
 */
class Entry extends WebzashAppModel {

	/* Validation rules for the Entry table */
	public $validate = array(
		'tag_id' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Tag id is not a valid number',
				'required'   => true,
				'allowEmpty' => true,
			),
			'rule2' => array(
				'rule' => array('maxLength', 11),
				'message' => 'Tag id length cannot be more than 11',
				'required'   => true,
				'allowEmpty' => true,
			),
			'rule3' => array(
				'rule' => 'validTag',
				'message' => 'Tag id is not valid',
				'required'   => true,
				'allowEmpty' => true,
			),
		),
		'entrytype_id' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Entry type cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'numeric',
				'message' => 'Entry type is not a valid number',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 11),
				'message' => 'Entry type length cannot be more than 11',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule4' => array(
				'rule' => 'validEntrytype',
				'message' => 'Entry type is not valid',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'number' => array(
			'rule1' => array(
				'rule' => 'numeric',
				'message' => 'Entry number is not a valid number',
				'required'   => true,
				'allowEmpty' => true,
			),
			'rule2' => array(
				'rule' => array('maxLength', 11),
				'message' => 'Entry number length cannot be more than 11',
				'required'   => true,
				'allowEmpty' => true,
			),
			'rule3' => array(
				'rule' => 'isUniqueEntryNumber',
				'message' => 'Entry number already exists',
				'required'   => true,
				'allowEmpty' => true,
			),
		),
		'date' => array(
			'rule1' => array(
				'rule' => 'fullDateTime',
				'message' => 'Invalid value for entry date',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'afterStart',
				'message' => 'Entry date should be after financial year start',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => 'beforeEnd',
				'message' => 'Entry date should be before financial year end',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'dr_total' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Debit total cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isAmount',
				'message' => 'Debit total is not a valid amount',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 28),
				'message' => 'Debit total length cannot be more than 28',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'cr_total' => array(
			'rule1' => array(
				'rule' => 'notEmpty',
				'message' => 'Credit total cannot be empty',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule2' => array(
				'rule' => 'isAmount',
				'message' => 'Credit total is not a valid amount',
				'required'   => true,
				'allowEmpty' => false,
			),
			'rule3' => array(
				'rule' => array('maxLength', 28),
				'message' => 'Credit total length cannot be more than 28',
				'required'   => true,
				'allowEmpty' => false,
			),
		),
		'narration' => array(
		),
	);

/**
 * Validation - Check if entry type is valid
 */
	public function validEntrytype($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		/* Load the Tag model */
		App::import("Webzash.Model", "Entrytype");
		$Entrytype = new Entrytype();

		if ($Entrytype->exists($value)) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Validation - Check if entry number is unique within the entry type
 */
	public function isUniqueEntryNumber($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		/* Check if any entry number exists within the same entry type */
		$count = $this->find('count', array(
			'conditions' => array(
				'Entry.number' => $value,
				'Entry.entrytype_id' => $this->data['Entry']['entrytype_id'],
			),
		));

		if ($count != 0) {
			return false;
		} else {
			return true;
		}
	}

/**
 * Validation - Check if tag_id is a valid id
 */
	public function validTag($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		/* Load the Tag model */
		App::import("Webzash.Model", "Tag");
		$Tag = new Tag();

		if ($Tag->exists($value)) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Validation - Check if value is a proper decimal number with 2 decimal places
 */
	public function isAmount($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];
		if (preg_match('/^[0-9]{0,23}+(\.[0-9]{0,2})?$/', $value)) {
			return true;
		} else {
			return false;
		}
	}


/**
 * Validation - Check if valid datetime
 */
	public function fullDateTime($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		$unixtime = strtotime($value);

		if (FALSE !== $unixtime) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Validation - Check if entry date is after financial year start
 */
	public function afterStart($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		$startdate = strtotime(CakeSession::read('startDate'));
		$entrydate = strtotime($value);

		if ($startdate < $entrydate) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Validation - Check if entry date is before financial year end
 */
	public function beforeEnd($data) {
		$values = array_values($data);
		if (!isset($values)) {
			return false;
		}
		$value = $values[0];

		$enddate = strtotime(CakeSession::read('endDate'));
		$entrydate = strtotime($value);

		if ($enddate >= $entrydate) {
			return true;
		} else {
			return false;
		}
	}

/**
 * Calculate the next number for a entry based on entry type
 */
	public function nextNumber($id)	{
		$max = $this->find('first', array(
			'conditions' => array('Entry.entrytype_id' => $id),
			'fields' => array('MAX(Entry.number) AS max'),
		));
		if (empty($max[0]['max'])) {
			$maxNumber = 0;
		} else {
			$maxNumber = $max[0]['max'];
		}
		return $maxNumber + 1;
	}
}