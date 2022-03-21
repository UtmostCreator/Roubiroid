<!--C:\OpenServer\domains\Roubiroid\resources\views\login.advanced.php-->
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
    <h1>Please Login</h1>
<?= $form->input($model, 'email', [
    'type' => 'email',
    'class' => 'custom-class',
    'required' => true,
    'note' => 'Use your email to login',
])->label(); ?>
<?= $form->input($model, 'password', [
    'class' => 'custom-class',
])->passwordField(true)->label(); ?>
    <button type="submit" class="btn btn-primary">Submit</button>
<?= Form::end(); ?>
