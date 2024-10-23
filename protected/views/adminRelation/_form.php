<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'relation-form',
			'enableAjaxValidation' => false,
		)); ?>

		<p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php if(Yii::app()->user->hasFlash('error')):?>
            <div class="alert alert-danger">
                <?php echo Yii::app()->user->getFlash('error'); ?>
            </div>
        <?php endif; ?>

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
		$this->widget('application.components.controls.DropdownField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'related_doi',
			'listDataOptions' => [
				'data' => Util::getDois(),
				'valueField' => 'identifier',
				'textField' => 'identifier',
			],
			'inputOptions' => [
				'required' => true,
			],
		]);
		$this->widget('application.components.controls.DropdownField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'relationship_id',
			'listDataOptions' => [
				'data' => Relationship::model()->findAll(),
				'valueField' => 'id',
				'textField' => 'name',
			],
		]);
        if ('insert' === $model->getScenario()) {
            $this->widget('application.components.controls.CheckBoxField', [
                'form' => $form,
                'model' => $model,
                'attributeName' => 'add_reciprocal',
                'label' => 'Do you want to add a reciprocal relation model'
            ]);
        }
		?>

		<div class="pull-right btns-row">
			<a href="/adminRelation/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>
