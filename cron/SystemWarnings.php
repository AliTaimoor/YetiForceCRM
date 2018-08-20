<?php
/**
 * System warnings cron.
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */
$html = '';
foreach (\App\SystemWarnings::getWarnings('all') as $warning) {
	if ($warning->getFolder() === 'YetiForce') {
		continue;
	}
	$html .= '<h2>' . App\Language::translate($warning->getTitle(), 'Settings:SystemWarnings') . '</h2>';
	$html .= '<p>' . $warning->getDescription() . '</p>';
	$html .= '<hr>';
}
if (empty($html)) {
	return;
}
$html .= '<br><br>' . AppConfig::main('site_URL');
$dataReader = (new \App\Db\Query())->select(['name', 'email'])->from('s_#__companies')->where(['default' => 1])->createCommand()->query();
while ($row = $dataReader->read()) {
	$html .= ' - ' . $row['name'];
	if ($row['email']) {
		$html .= ' - ' . $row['email'];
	}
}
$mails = (new \App\Db\Query())->select('email1')->from('vtiger_users')->where(['is_admin' => 'on', 'status' => 'Active'])->column();
if ($mails) {
	\App\Mailer::sendFromTemplate([
		'to' => $mails,
		'template' => 'SystemWarnings',
		'warnings' => $html,
	]);
}
