<?php

use app\core\form\Form;

/** @var \app\models\ContactForm $model */
/** @var \app\core\View $this */
$this->title = 'Contact us';

$form = Form::begin('', 'post', ['enctype' => Form::ENCTYPE_DEFAULT, 'class' => 'custom-class']); ?>

<h1>Contact Us</h1>

<?= $form->input($model, 'email', [
    'type' => 'email',
    'required' => true,
    'note' => 'Use your email to login',
])->label(); ?>
<?= $form->input($model, 'subject')->label(); ?>
<?= $form->textarea($model, 'body', ['rows' => 8])->label(); ?>
<button type="submit" class="btn btn-primary">Submit</button>

<?= Form::end(); ?>
