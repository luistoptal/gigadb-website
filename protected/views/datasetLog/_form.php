<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'dataset-log-form',
			'enableAjaxValidation' => false,
		)); ?>

		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<?php if ($model->hasErrors()) : ?>
			<div class="alert alert-danger">
				<?php echo $form->errorSummary($model); ?>
			</div>
		<?php endif; ?>


		<?php
		$this->widget('application.components.controls.DropdownField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'dataset_id',
			'listDataOptions' => [
				'data' => Util::getDois(),
				'valueField' => 'id',
				'textField' => 'identifier',
			],
			'inputOptions' => [
				'required' => true,
			],
		]);

		$this->widget('application.components.controls.TextArea', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'message',
			'inputOptions' => [
				'rows' => 6,
				'cols' => 50
			],
		]);
		?>

		<div class="pull-right">
			<a href="/datasetLog/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>