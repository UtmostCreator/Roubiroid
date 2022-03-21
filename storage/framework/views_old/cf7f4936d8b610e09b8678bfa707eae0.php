<!--C:\OpenServer\domains\Roubiroid\resources\views\login-ukr.advanced.php-->
<?php
use Framework\View\form\Form;
$this->title = 'Login';
?>

<?php $this->extends('layouts/auth'); ?>
<?php
$form = Form::begin($logInAction, 'post', [
    'enctype' => Form::ENCTYPE_DEFAULT,
    'class' => 'custom-class'
]); ?>
    <input type="hidden" name="csrf" value="<?php print $this->escape( csrf() ); ?>" />
    <h1>Вхід до системи</h1>
<?= $form->input($model, 'email', [
    'type' => 'email',
    'class' => 'custom-class',
    'placeholder' => 'Електронна Адреса',
    'note' => 'Використовуйте свій email для входу',

])->label(); ?>
<?= $form->input($model, 'password', [
    'class' => 'custom-class',
    'placeholder' => 'Пароль',
])->passwordField(true)->label(); ?>
    <button type="submit" class="btn btn-primary">Вхід</button>
<?= Form::end(); ?>
