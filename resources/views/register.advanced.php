<?php

use Framework\View\form\Form;
use models\User;

//\Modules\DD\DD::dd($_SERVER); //phpinfo()
/** @var User $model */

$form = Form::begin('', 'post', ['enctype' => Form::ENCTYPE_DEFAULT, 'class' => 'custom-class']); ?>
@extends('layouts/auth')

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
    'class' => 'custom-class',
    'note' => 'some Note goes here',
])->passwordField(true)->label(); ?>
<?= $form->input($model, 'confirmPassword', [
    'class' => 'custom-class',
    'note' => 'some Note goes here',
])->passwordField(true)->label(); ?>
<!--    <div class="row">-->
<!--        <div class="col">-->
<!--            <div class="mb-3">-->
<!--                <label for="firstname" class="form-label">Last Name</label>-->
<!--                <input type="firstname" class="form-control" id="firstname" name="firstname">-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="col">-->
<!--            <div class="mb-3">-->
<!--                <label for="lastname" class="form-label">First Name</label>-->
<!--                <input type="lastname" class="form-control" id="lastname" name="lastname">-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!---->
<!---->
<!--    <div class="mb-3">-->
<!--        <label for="email" class="form-label">Email</label>-->
<!--        <input type="email" class="form-control" id="email" name="email">-->
<!--    </div>-->
<!--    <div class="mb-3">-->
<!--        <label for="password" class="form-label">Password</label>-->
<!--        <input type="password" class="form-control" id="password" name="password">-->
<!--    </div>-->
<!--    <div class="mb-3">-->
<!--        <label for="confirmPassword" class="form-label">Confirm Password</label>-->
<!--        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">-->
<!--    </div>-->
<button type="submit" class="btn btn-primary">Submit</button>

<?= Form::end(); ?>
