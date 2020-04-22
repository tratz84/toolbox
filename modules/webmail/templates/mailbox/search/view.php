

<?= include_component('webmail', 'mailbox/mail', 'mailactions', ['emailId' => $emailId]) ?>

<input type="hidden" id="emailId" value="<?= esc_attr($emailId) ?>" />

<iframe src="<?= $url_view_mail ?>" style="width:100%; height: calc(100% - 78px);" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox"></iframe>

