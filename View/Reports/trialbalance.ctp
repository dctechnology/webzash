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

/**
 * Display chart of accounts
 *
 * @account AccountList group account
 * @c int counter for number of level deep the account is
 * @THIS this $this CakePHP object passed inside function
 */
function print_account_chart($account, $c = 0, $THIS)
{
	$counter = $c;

	/* Print groups */
	if ($account->id != 0) {
		echo '<tr class="tr-group">';
		echo '<td class="td-group">';
		echo print_space($counter);
		/* If group id less than 4 bold the primary groups */
		if ($account->id <= 4) {
			echo '<strong>' .  $account->name. '</strong>';
		} else {
			echo $account->name;
		}
		echo '</td>';
		echo '<td>Group</td>';

		echo '<td>';
		echo toCurrency($account->op_total_dc, $account->op_total);
		echo '</td>';

		echo '<td>' . toCurrency('D', $account->dr_total) . '</td>';

		echo '<td>' . toCurrency('C', $account->cr_total) . '</td>';

		if ($account->cl_total_dc == 'D') {
			echo '<td>' . toCurrency('D', $account->cl_total) . '</td>';
			echo '<td></td>';
		} else {
			echo '<td></td>';
			echo '<td>' . toCurrency('C', $account->cl_total) . '</td>';
		}

		echo '</tr>';
	}

	/* Print child ledgers */
	if (count($account->children_ledgers) > 0) {
		$counter++;
		foreach ($account->children_ledgers as $id => $data) {
			echo '<tr class="tr-ledger">';
			echo '<td class="td-ledger">';
			echo print_space($counter);
			echo $THIS->Html->link($data['name'], array('controller' => 'reports', 'action' => 'ledgerstatement', 'ledgerid' => $data['id']));
			echo '</td>';
			echo '<td>Ledger</td>';

			echo '<td>';
			echo toCurrency($data['op_total_dc'], $data['op_total']);
			echo '</td>';

			echo '<td>' . toCurrency('D', $data['dr_total']) . '</td>';

			echo '<td>' . toCurrency('C', $data['cr_total']) . '</td>';

			if ($data['cl_total_dc'] == 'D') {
				echo '<td>' . toCurrency('D', $data['cl_total']) . '</td>';
				echo '<td></td>';
			} else {
				echo '<td></td>';
				echo '<td>' . toCurrency('C', $data['cl_total']) . '</td>';
			}

			echo '</tr>';
		}
		$counter--;
	}

	/* Print child groups recursively */
	foreach ($account->children_groups as $id => $data) {
		$counter++;
		print_account_chart($data, $counter, $THIS);
		$counter--;
	}
}

function print_space($count)
{
	$html = '';
	for ($i = 1; $i <= $count; $i++) {
		$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	}
	return $html;
}

echo '<table>';
	echo '<th>' . __d('webzash', 'Account Name') . '</th>';
	echo '<th>' . __d('webzash', 'Type') . '</th>';
	echo '<th>' . __d('webzash', 'O/P Balance') . '</th>';
	echo '<th>' . __d('webzash', 'Debit Total') . '</th>';
	echo '<th>' . __d('webzash', 'Credit Total') . '</th>';
	echo '<th>' . __d('webzash', 'C/L Debit') . '</th>';
	echo '<th>' . __d('webzash', 'C/L Credit') . '</th>';

	print_account_chart($accountlist, 0, $this);

	echo '<tr>';
	echo '<td>' . __d('webzash', 'TOTAL') . '</td>';
	echo '<td></td><td></td>';
	echo '<td>' . toCurrency('D', $accountlist->dr_total) . '</td>';
	echo '<td>' . toCurrency('C', $accountlist->cr_total) . '</td>';
	if (calculate($accountlist->dr_total, $accountlist->cr_total, '==')) {
		echo '<td>' . $this->Html->image('Webzash.match.png', array('alt' => 'match')) . '</td>';
	} else {
		echo '<td>' . $this->Html->image('Webzash.nomatch.png', array('alt' => 'mismatch')) . '</td>';
	}
	echo '<td></td>';
	echo '</tr>';

echo '</table>';