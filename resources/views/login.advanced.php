<?php

use Framework\View\form\Form;
use models\User;

//\Modules\DD\DD::dd($_SERVER); //phpinfo()
/** @var User $model */
/** @var \Framework\View $this */
$this->title = 'Login';
?>
@extends('layouts/auth')
<?php
$form = Form::begin('', 'post', ['enctype' => Form::ENCTYPE_DEFAULT, 'class' => 'custom-class']); ?>


<h1>Create an account</h1>

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
