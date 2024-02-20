<div class="container">
    <?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle' => 'View Sample #' . $model->id,
        'breadcrumbItems' => [
            ['label' => 'Admin', 'href' => '/site/admin'],
            ['label' => 'Manage', 'href' => '/adminSample/admin'],
            ['isActive' => true, 'label' => 'View'],
        ]
    ]);
    ?>

    <?php $this->widget('zii.widgets.CDetailView', array(
        'data' => $model,
        'attributes' => array(
            'id',
            'name',
            'species_id',
            array(
                'name' => 'attributesList',
                'type' => 'raw',
                'value' => $model->getAttributesList(),
            ),
        ),
        'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
        'itemCssClass' => array('odd', 'even'),
        'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'
    )); ?>

</div>