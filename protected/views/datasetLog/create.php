<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Create Update Log',
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['label' => 'Manage', 'href' => 'admin'],
      ['isActive' => true, 'label' => 'Create'],
    ]
  ]);
  ?>

  <?php echo $this->renderPartial('_form', array('model' => $model)); ?>
</div>