<?php

use Framework\View\form\Form;
use models\User;

/* @var User $model */
?>
@extends('layouts/auth')
<?php $form = Form::begin('', 'post', ['enctype' => Form::ENCTYPE_DEFAULT, 'class' => 'custom-class']); ?>

<h1>Create an account</h1>
<div class="row">
    <div class="col">
        <?= $form->input($model, 'firstname', [
            'class' => 'custom-class',
            'note' => 'some Note goes here',
        ])->label(); ?>
    </div>
    <div class="col">
        <?= $form->input($model, 'lastname', [
            'class' => 'custom-class',
            'note' => 'some Note goes here',
        ])->label(); ?>
    </div>
</div>


<?= $form->input($model, 'email', [
    'type' => 'email',
    'class' => 'custom-class',
    'required' => true,
    'note' => 'some Note goes here',
])->label(); ?>
<?= $form->input($model, 'password', [
    'id' => 'password',
    'class' => 'custom-class',
    'note' => 'some Note goes here',
])->passwordField(true)->label(); ?>
<?= $form->input($model, 'confirmPassword', [
    'id' => 'confirmPassword',
    'class' => 'custom-class',
    'note' => 'some Note goes here',
])->passwordField(true)->label(); ?>
<button type="submit" class="btn btn-primary">Submit</button>

<?= Form::end(); ?>
