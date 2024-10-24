<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css" />

<?php
$title = strlen($model->title) > 100 ? strip_tags(substr($model->title, 0, 100)) . " ..." : strip_tags($model->title);
$this->pageTitle = "GigaDB Dataset - DOI 10.5524/" . $model->identifier . " - " . $title;

$fileDataProvider = $files->getDataProvider();
$sampleDataProvider = $samples->getDataProvider();

?>

<?php if (Yii::app()->user->hasFlash('mockupMode')) { ?>
    <div class="alert alert-info">
        <?php echo Yii::app()->user->getFlash('mockupMode'); ?>
    </div>
<?php } ?>

<?php $this->renderPartial('_sample_setting', array('columns' => $columns, 'pageSize' => $sampleDataProvider->getPagination()->getPageSize())); ?>
<?php $this->renderPartial('_files_setting', array('setting' => $setting, 'pageSize' => $fileDataProvider->getPagination()->getPageSize())); ?>

<div class="content">
    <div class="container dataset-view-container">
        <div class="subsection">
            <div class="media">
                <div class="media-left">
                    <?php if ($model->image) {
                        $url = $model->image->isUrlValid() ? $model->image->url : "https://assets.gigadb-cdn.net/live/images/datasets/no_image.png";

                    ?>
                        <a href="<?= $url ?>">
                            <?= CHtml::image(
                                $url,
                                $model->image->tag,
                                array(
                                    'class' => 'media-object',
                                    'title' => $model->image->tag . ' License: ' . $model->image->license . ' Source: ' . $model->image->source . ' Photographer: ' . $model->image->photographer
                                )
                            ); ?>
                        </a>
                    <?php } ?>

                </div>
                <div class="media-body">
                    <h1 class="left-border-title left-border-title-lg"><?= $mainSection->getHeadline()['title']; ?></h1>
                    <p class="dataset-release-date-text">Dataset type: <?= $mainSection->getHeadline()['types']; ?> <br> Data released on <?= $mainSection->getHeadline()['release_date'] ?></p>
                    <div class="color-background color-background-block dataset-color-background-block">
                        <p><?= $mainSection->getReleaseDetails()['authors'] ?> (<?= $mainSection->getReleaseDetails()['release_year'] ?>): <?= $mainSection->getReleaseDetails()['dataset_title'] . ' ' . ($mainSection->getReleaseDetails()['publisher'] ?? '<span class="label label-danger">NO PUBLISHER SET</span>') . '. '; ?><a href="https://doi.org/10.5524/<?php echo $model->identifier; ?>">https://doi.org/10.5524/<?php echo $model->identifier; ?></a></p>
                        <div id="dataset-block-wrapper">
                            <div id="badge-div">
                                <span class="doi-badge"><span class="badge">DOI</span><span class="badge">10.5524/<?php echo $model->identifier; ?></span></span>
                            </div>
                            <?php if ($model->upload_status == 'Published') { ?>
                                <div id="dropdown-div">
                                    <div class="dropdown-box">
                                        <button id="CiteDataset" class="btn background-btn drop-citation-btn dropdown-toggle" type="button" data-toggle="dropdown">Cite Dataset <span class="caret"></span></button>
                                        <?php
                                        $url = 'https://data.datacite.org/text/x-bibliography/10.5524/' . $model->identifier;
                                        try {
                                            $textFile = DownloadService::downloadFile($url);
                                            $showButton = true;
                                        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                                            $showButton = false;
                                            yii::log($e->getMessage(), "error");
                                        }
                                        ?>
                                        <?php if ($showButton == 'true') { ?>
                                            <script>
                                                function showText() {
                                                    var textWindow = window.open();
                                                    textWindow.document.write(`<?php echo $textFile; ?>`);
                                                }
                                            </script>
                                        <?php } else { ?>
                                            <script>
                                                $("#CiteDataset").addClass("hide");
                                            </script>
                                        <?php } ?>
                                        <ul class="dropdown-menu" aria-labelledby="CiteDataset">
                                            <li><a role="link" id="Text" onclick="showText()" target="_blank" tabindex="0" aria-label="Text (opens in a new tab)">Text</a></li>
                                            <li><a id="citeRis" href='https://data.datacite.org/application/x-research-info-systems/10.5524/<?php echo $model->identifier; ?>' target="_self" aria-label="Download RIS file">RIS</a></li>
                                            <li><a id="citeBibTeX" href='https://data.datacite.org/application/x-bibtex/10.5524/<?php echo $model->identifier; ?>' target="_self" aria-label="Download bibtex file">BibTeX</a></li>
                                        </ul>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="subsection">
                <p><?php echo $mainSection->getDescription()['description'] ?></p>
            </div>

            <div class="subsection">
                <?php if (!empty($mainSection->getKeywords())) { ?>
                    <p>Keywords:</p>
                    <ul class="list-inline">
                        <? foreach ($mainSection->getKeywords() as $keyword_link) {
                            echo "<li>$keyword_link</li>";
                        }
                        ?>
                    </ul>
                <?php } ?>
                <div class="pull-right">
                    <p>
                        <?php
                        foreach (array_values($mainSection->getCitationsLinks()) as $citation) {
                            echo $citation;
                        }
                        ?>

                    </p>
                </div>
            </div>
            <div class="subsection btns-row">
                <span class="content-popup" <?= $email != '' ? '' : 'data-content="Please login to contact submitter"' ?> data-original-title="">
                    <a class="btn <?= $email != '' ? 'background-btn-o' : 'inactive-btn-o' ?>" <?= $email != '' ? 'href="mailto:' . $email . '"' : 'href="#"' ?>>
                        Contact Submitter
                    </a>
                </span>
                <? if (!Yii::app()->user->isGuest && null == Author::findAttachedAuthorByUserId(Yii::app()->user->id)) { ?>
                    <span title="click to claim the dataset and link your user account to an author" data-toggle="tooltip" data-placement="bottom">
                        <a href="#myModal" role="button" class="btn background-btn-o" data-toggle="modal">
                            Your dataset?
                        </a>
                    </span>
                    <!-- Modal -->
                    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    <h2 class="modal-title h4" id="myModalLabel">Select an author to link to your Gigadb User ID</h2>
                                    <div id="message"></div>
                                    <div id="advice"></div>
                                </div>
                                <?php echo CHtml::beginForm('/userCommand/claim', 'GET'); ?>
                                <div class="modal-body text-center">
                                    <?php if (count($model->authors) > 0) { ?>
                                        <div class="btns-row">
                                            <?php foreach ($model->authors as $author) { ?>
                                                <a href="#" role="button" class="btn btn-link js-claim-button" data-author-id="<?= $author->id ?>"
                                                    id="claim_button_<?= $author->id ?>">
                                                <?= $author->first_name . ' ' . $author->middle_name . ' ' . $author->surname ?>
                                                </a>
                                                <span><? echo $author->orcid ? " (orcid id:" . $author->orcid . ")" : "" ?></span>
                                            <? } ?>
                                        </div>
                                    <? } ?>
                                </div>
                                <div class="modal-footer">
                                    <div class="btns-row pull-right">
                                        <button type="reset" class="btn btn-inverse" data-dismiss="modal" aria-hidden="true">Close</button>
                                        <input type="hidden" id="dataset_id" name="dataset_id" value="<? echo $model->id ?>" />
                                        <a href="#" id="cancel_button" class="btn danger-btn">Cancel current claim</a>
                                    </div>
                                </div>
                                <?php echo CHtml::endForm(); ?>
                            </div>
                        </div>
                    </div>
                <? } ?>
            </div>



            <div class="subsection">
                <?php if ($model->fairnuse) {
                    if ((time() < strtotime($model->fairnuse))) { ?>
                        <img src="/images/fair_use2.gif" alt="policy" style="" />
                        <p>
                            These data are made available pre-publication under the Fort Lauderdale rules.
                            Please respect the rights of the data producers to publish their whole dataset analysis first.
                            The data is being made available so that the research community can make use of them for more
                            focused studies without having to wait for publication of the whole dataset analysis paper.
                            If you wish to perform analyses on this complete dataset, please contact the authors directly
                            so that you can work in collaboration rather than in competition.
                        </p>
                        <p><strong>This dataset fair use agreement is in place until <?= strftime('%d %B %Y', strtotime($model->fairnuse)) ?></strong></p>
                <?php }
                } ?>
            </div>
            <div class="subsection">
                <div class="underline-title">
                    <div>
                        <h2 class="h4">Additional details</h2>
                    </div>
                </div>
                <?php
                $publications = $connections->getPublications();
                if (!empty($publications)) { ?>
                    <h3 class="h5"><strong><?= Yii::t('app', 'Read the peer-reviewed publication(s):') ?></strong></h3>
                    <ul class="list-unstyled citation-list">
                        <? foreach ($publications as $publication) {
                          ?>
                          <li>
                          <?
                            echo $publication['citation'] . $publication['pmurl'];
                          ?>
                          </li>
                          <?
                        }
                        ?>
                    </ul>
                <?php } ?>

                <?php
                $relations = $connections->getRelations();
                if (count($relations) > 0) {
                ?>
                    <?php $this->renderPartial('_connections', array('relations' => $relations)); ?>

                <?php } ?>
                <p>
                    <?php
                    $mainbodyExternalLinks = $links->getDatasetExternalLinksTypesAndCount(["Additional information", "Genome browser", "Github links"]);
                    foreach (array_keys($mainbodyExternalLinks) as $linkType) {
                        echo "<h3 class=\"h5\"><strong>${linkType}:</strong></h3>";
                        foreach ($links->getDatasetExternalLinks([$linkType]) as $link) {
                            echo '<p>' . CHtml::link($link['url'], $link['url'], array("title" => $linkType . " for dataset " . $model->identifier)) . '</p>';
                        }
                    }
                    ?>
                </p>


                <?php if (count($accessions) > 0) { ?>
                    <?php
                    $primary_links = $accessions->getPrimaryLinks();
                    $secondary_links = $accessions->getSecondaryLinks();
                    ?>

                    <?php if (!empty($primary_links)) { ?>
                        <h3 class="h5"><strong><?= Yii::t('app', 'Accessions (data included in GigaDB):') ?></strong></h3>
                        <p>
                            <?php foreach ($primary_links as $link) {
                                echo $link->format;
                            } ?>
                        </p>
                    <?php } ?>

                    <?php if (!empty($secondary_links)) { ?>
                        <h3 class="h5"><strong><?= Yii::t('app', 'Accessions (data not in GigaDB):') ?></strong></h3>
                        <p>
                            <?php foreach ($secondary_links as $link) {
                                echo $link->format;
                            } ?>
                        </p>
                    <?php } ?>

                <?php } //if (count($accessions) > 0)
                ?>

                <?php $projects = $connections->getProjects();
                if (count($projects) > 0) { ?>
                    <h3 class="h5"><strong><?= Yii::t('app', 'Projects:') ?></strong></h3>
                    <p>
                        <? foreach ($projects as $project) {
                            echo $project['format'];
                            echo "<br/>";
                        }
                        ?>
                    </p>
                <? } ?>

            </div>

            <section>
                <?php
                $protocol = array();
                $jb = array();
                $dmodel = array();
                $codeocean = array();
                Yii::log("Nb of files: " . $fileDataProvider->getTotalItemCount(), "debug");
                ?>
                <ul class="nav nav-tabs nav-border-tabs" role="tablist">
                    <?php if (count($model->samples) > 0) {
                    ?>
                        <li role="presentation" id="p-sample"><a href="#sample" aria-controls="sample" role="tab" data-toggle="tab">Sample</a></li>
                    <?php }
                    ?>
                    <?php if ($fileDataProvider->getTotalItemCount() > 0) {
                        if (count($model->samples) < 1) {
                    ?>
                            <li role="presentation" id="p-file" class="active"><a href="#files" aria-controls="files" role="tab" data-toggle="tab">Files</a></li>
                        <?php } else {
                        ?>
                            <li role="presentation" id="p-file"><a href="#files" aria-controls="files" role="tab" data-toggle="tab">Files</a></li>
                    <?php }
                    }
                    ?>
                    <?php if (count($model->datasetFunders) > 0) {
                    ?>
                        <li role="presentation" id="p-funding"><a href="#funding" aria-controls="funding" role="tab" data-toggle="tab">Funding</a></li>
                    <?php }
                    ?>
                    <?php
                    foreach ($links->getDatasetExternalLinksTypesNames(["Protocols.io", "JBrowse", "3D Models", "Code Ocean"]) as $linkType => $linkCode) {
                    ?>
                        <li role="presentation" id="p-<?= $linkCode ?>"><a href="#<?= $linkCode ?>" aria-controls="<?= $linkCode ?>" role="tab" data-toggle="tab"><?= $linkType ?></a></li>
                    <?php
                    }
                    ?>

                    <li role="presentation" id="p-history"><a href="#history" aria-controls="history" role="tab" data-toggle="tab">History</a></li>

                </ul>


                <div class="tab-content">
                <?php
                    if ($sampleDataProvider->getTotalItemCount() > 0) {
                        $samplesPerPage = $sampleDataProvider->getItemCount();
                        $totalNbSamples = $sampleDataProvider->getTotalItemCount();

                        if (count($model->samples) > 0) {
                    ?>
                        <div role="tabpanel" class="tab-pane active" id="sample">

                            <p class="pull-left">
                              Click on a table column to sort the results.
                            </p>
                            <a id="samples_table_settings" class="btn btn-default pull-right" data-toggle="modal" data-target="#samples_settings" href="#"><span class="glyphicon glyphicon-adjust"></span>Table Settings</a>
                            <table id="samples_table" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th title="User-specified name or identifier of the sample object. Note: a DNA sample and an RNA sample from the same donor are classed as two separate samples.">Sample ID</th>
                                        <th title="A well recognized commonly used name of the species, usually this is a synonym held in the NCBI taxonomy for the tax ID provided.">Common Name</th>
                                        <th title="The scientific binomial name of the species, usually this is in direct accordance with the NCBI taxonomy ID provided.">Scientific Name</th>
                                        <th title="This is a list of Key:Value pairs, where the Keys are from our Attributes list, and the Value is the specific value for the sample. See our metadata guide for the Attributes list with definitions of all available attributes.">Sample Attributes</th>
                                        <th title="Species taxonomy ID of the sampled species, we currently use the NCBI taxonomy as the source of this identifier.">Taxonomic ID</th>
                                        <th title="The preferred display name used by NCBI taxonomy for the tax ID provided">Genbank Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $sample_models = $sampleDataProvider->getData();

                                    foreach ($sample_models as $sample) { ?>
                                        <tr>
                                            <td><?= $sample['linkName'] ?></td>
                                            <td><?= $sample['common_name'] ?></td>
                                            <td><?= $sample['scientific_name'] ?></td>
                                            <td><?= $sample['displayAttr'] ?></td>
                                            <td><?= $sample['taxonomy_link'] ?></td>
                                            <td><?= $sample['genbank_name'] ?></td>
                                        </tr>
                                    <?php } ?>

                                </tbody>
                            </table>
                            <div class="table-footer">
                                <?php
                                if ($samplesPerPage <> $totalNbSamples) {
                                  ?>
                                  <div class="pagination-wrapper">
                                  <?
                                    $this->widget('SiteLinkPager', array(
                                        'id' => 'samples-pager',
                                        'pages' => $sampleDataProvider->getPagination(),
                                    ));
                                ?>
                                <div class="page-selector">
                                  <button class="btn background-btn-o" id="samplesPageButton" onclick="goToSamplesPage()">Go to page</button>
                                  <input type="number" id="samplesPageInput" class="page_box" onkeypress="detectEnterKeyPress(event)" min="1" max="<?= $sampleDataProvider->getPagination()->getPageCount() ?>" aria-label="Enter page number">
                                  <span class="page-selector-label"> of <?php echo $sampleDataProvider->getPagination()->getPageCount() ?></span>
                                </div>
                                </div>
                                <?php
                              }
                              ?>
                                <div class="pull-right">
                                    <div class="summary">Displaying <?php echo $samplesPerPage ?> samples of <?php echo $totalNbSamples ?></div>
                                </div>
                                </div>

                        </div>
                    <?php
                      }
                    }
                    ?>
                    <?php
                    if ($fileDataProvider->getTotalItemCount() > 0) {
                        $filesPerPage = $fileDataProvider->getItemCount();
                        $totalNbFiles = $fileDataProvider->getTotalItemCount();

                        if (count($model->samples) > 0) {
                    ?>
                            <div role="tabpanel" class="tab-pane" id="files">
                            <?php } else { ?>
                                <div role="tabpanel" class="tab-pane active" id="files">
                                <?php   } ?>
                                <p class="pull-left">
                                  Click on a table column to sort the results.
                                </p>
                                <a id="files_table_settings" class="btn btn-default pull-right" data-toggle="modal" data-target="#files_settings" href="#"><span class="glyphicon glyphicon-adjust"></span>Table Settings</a>
                                <br>
                                <br>
                                <table id="files_table" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th title="The name of the file. Click header to sort by A-Z/Z-A.">File Name</th>
                                            <th title="Short description of file contents. Click header to sort by A-Z/Z-A.">Description</th>
                                            <th title="Name or ID of sample used to generate this file.">Sample ID</th>
                                            <th title="The type of data in the file, see [help](http://gigadb.org/site/help#vocabulary) page for definitions of individual data types.  Click header to sort by A-Z/Z-A.">Data Type</th>
                                            <th title="The format of the file, see [help](http://gigadb.org/site/help#vocabulary) page for definitions of individual file formats. Click header to sort by A-Z/Z-A.">File Format</th>
                                            <th title="The size on disk of the file. Click header to sort by A-Z/Z-A.">Size</th>
                                            <th title="Date of release of the file, see the history log for details of any changes made after initial release date. Click header to sort by A-Z/Z-A.">Release Date</th>
                                            <th title="Additional information about the file presented as Key:Value pairs.">File Attributes</th>
                                            <th title="The direct link to the files server location.">Download</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $file_models = $fileDataProvider->getData();
                                        foreach ($file_models as $file) {
                                        ?>
                                            <tr>
                                                <td><?= $file['nameHtml'] ?></td>
                                                <td><?= $file['description'] ?></td>
                                                <td><?php
                                                    //TODO: huge performance issue with large numbers of fileDatasetKeywordsTest.php:49, manifesting when disabling cache
                                                    //                                        $file_samples = $files->formatDatasetFilesSamples(3, $file['id']) ;
                                                    //                                        echo $file_samples[0]['visible'];
                                                    //                                        echo $file_samples[0]['hidden'];
                                                    //                                        echo $file_samples[0]['more_link'];
                                                    ?></td>
                                                <td><?= $file['type'] ?></td>
                                                <td><?= $file['format'] ?></td>
                                                <td><?= $file['sizeUnit'] ?></td>
                                                <td><?= $file['date_stamp'] ?></td>
                                                <td><?= $file['attrDesc'] ?></td>
                                                <td class="button-column">
                                                    <div class="icon-wrapper">
                                                        <a class="js-download-count fa fa-download fa-lg icon icon-download" href="<?= $file['location'] ?>" aria-label="Download <?= $file["name"] ?>"></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                                <div class="table-footer">
                                <?php
                                if ($filesPerPage <> $totalNbFiles) {
                                  ?>
                                  <div class="pagination-wrapper">
                                  <?
                                    $this->widget('SiteLinkPager', array(
                                        'id' => 'files-pager',
                                        'pages' => $fileDataProvider->getPagination(),
                                    ));
                                ?>
                                <div class="page-selector">
                                  <button class="btn background-btn-o" id="filesPageButton" onclick="goToFilesPage()">Go to page</button>
                                  <input type="number" id="filesPageInput" class="page_box" onkeypress="detectEnterKeyPress(event)" min="1" max="<?= $fileDataProvider->getPagination()->getPageCount() ?>" aria-label="Enter page number">
                                  <span class="page-selector-label"> of <?php echo $fileDataProvider->getPagination()->getPageCount() ?></span>
                                </div>
                                </div>
                                <?php } ?>
                                <div class="pull-right">
                                    <div class="summary">Displaying <?php echo $filesPerPage ?> files of <?php echo $totalNbFiles ?></div>
                                </div>
                                </div>

                                </div>
                            <?php } ?>

                            <?php
                            $funding = $mainSection->getFunding();
                            if (count($funding) > 0) {
                            ?>

                                <div role="tabpanel" class="tab-pane" id="funding">


                                    <table class="table table-bordered text-center">
                                        <thead>
                                            <tr>
                                                <th title="The name of the funding agency providing funding. Where possible this should be from the FundRef list of funding bodies (https://www.e-sciencecentral.org/funder/).">Funding body</th>
                                                <th title="The name of the person responsible for getting the award.">Awardee</th>
                                                <th title="The grant or contract number of the project that sponsored the effort.">Award ID</th>
                                                <th title="Some agencies have multiple award programs through which they distribute funding, if appropriate that information can be added here.">Comments</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($funding as $funder) { ?>
                                                <tr>
                                                    <td><?= $funder['funder_name'] ?></td>
                                                    <td><?= $funder['awardee'] ?></td>
                                                    <td><?= $funder['grant_award'] ?></td>
                                                    <td><?= $funder['comments'] ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>


                                </div>
                            <?php }
                            ?>

                            <?php
                            foreach ($links->getDatasetExternalLinksTypesNames(["Protocols.io", "JBrowse", "3D Models", "Code Ocean"]) as $linkType => $linkCode) {
                            ?>
                                <div role="tabpanel" class="tab-pane" id="<?= $linkCode ?>">
                                    <p><?= $linkType ?>:</p>
                                    <?php
                                    foreach ($links->getDatasetExternalLinks([$linkType]) as $link) {
                                        $p = $link['url'];
                                        switch ($linkType) {
                                            case "Protocols.io":
                                                $ps = HTTPSHelper::httpsize($p);
                                                echo "<iframe src=\"$ps\" style=\"width: 850px; height: 320px; border: 1px solid transparent;\"></iframe>";
                                                break;
                                            case "JBrowse":
                                                echo "<a href=\"$p\" target=\"_blank\">Open the JBrowse</a>";
                                                echo "<iframe src=\"$p\" style=\"width: 1000px; height: 520px; border: 1px solid transparent;\"></iframe>";
                                                echo "<br>";
                                                break;
                                            case "3D Models":
                                                echo "<iframe src=\"$p\" style=\"width: 950px; height: 520px; border: 1px solid transparent;\"></iframe>";
                                                break;
                                            case "Code Ocean":
                                                echo "<p>$p</p>";
                                                break;
                                        }
                                    }
                                    ?>
                                </div>
                            <?php
                            }
                            ?>

                            <div role="tabpanel" class="tab-pane" id="history">

                                <table class="table table-bordered text-center">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($mainSection->getHistory() as $log) { ?>
                                            <tr>
                                                <td><?= date('F j, Y', strtotime($log['created_at'])) ?></td>
                                                <td><?= $log['message'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                            </div>
                            </div>
            </section>
        </div>
    </div>





    <div class="clear"></div>

    <a href="/dataset/<?php echo $previous_doi ?>" class="fixed-btn-left" title="Previous dataset" aria-label="Previous dataset"><span class="fa fa-angle-left"></span></a>
    <a href="/dataset/<?php echo $next_doi ?>" title="Next dataset" class="fixed-btn-right" aria-label="Next dataset"><span class="fa fa-angle-right"></span></a>

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js" defer></script>
    <!-- Place this tag in your head or just before your close body tag. -->
    <script>
        document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded
            /* Document ready for Thumbnail Slider */
            /* ----------------------------------- */
            $(document).ready(function() {
                // If the related are more than 3 so we add the caroussel
                if ($('#myCarousel').attr('data-total') > 3) {
                    $('#myCarousel').carousel({
                        interval: 4000,
                        wrap: 'circular'
                    });
                }
                $('.tab-container').on("click", function() {
                    $(this).toggleClass('tab-show');
                    $(this).toggleClass('tab-hide');

                    var arrow = $(this).find('.tab-container__arrow')[0];
                    $(arrow).toggleClass('flip-vertical');
                });

                var url = location.pathname;
                var sample_index = url.lastIndexOf('Sample_');
                var file_index = url.lastIndexOf('File_');

                if (/Sample/.test(window.location.href)) {
                    $("#p-sample").addClass("active");
                    var e = document.getElementById('p-sample');
                    if (!!e && e.scrollIntoView) {
                        e.scrollIntoView();
                    }

                } else {
                    $("#p-sample").addClass("active");
                }
                if (/File/.test(window.location.href)) {

                    $("#p-sample").removeClass("active");
                    $("#sample").removeClass("tab-pane active");
                    $("#sample").addClass("tab-pane");
                    $("#p-file").addClass("active");
                    $("#files").addClass("active");

                    var e = document.getElementById('p-file');
                    if (!!e && e.scrollIntoView) {
                        e.scrollIntoView();
                    }


                }

                if (sample_index > 0 && file_index > 0) {
                    if (sample_index > file_index) {
                        $("#p-file").removeClass("active");
                        $("#files").removeClass("tab-pane active");
                        $("#files").addClass("tab-pane");
                        $("#p-sample").addClass("active");
                        $("#sample").addClass("active");
                        var e = document.getElementById('p-sample');
                        if (!!e && e.scrollIntoView) {
                            e.scrollIntoView();
                        }
                    } else {
                        $("#p-sample").removeClass("active");
                        $("#sample").removeClass("tab-pane active");
                        $("#sample").addClass("tab-pane");
                        $("#p-file").addClass("active");
                        $("#files").addClass("active");
                        var e = document.getElementById('p-file');
                        if (!!e && e.scrollIntoView) {
                            e.scrollIntoView();
                        }
                    }
                }
                var MyJSStringVar = "<?php print($flag); ?>"
                if (MyJSStringVar == 'file') {
                    $("#p-sample").removeClass("active");
                    $("#sample").removeClass("tab-pane active");
                    $("#sample").addClass("tab-pane");
                    $("#p-file").addClass("active");
                    $("#files").addClass("active");

                    var e = document.getElementById('p-file');
                    if (!!e && e.scrollIntoView) {
                        e.scrollIntoView();
                    }
                }
                if (MyJSStringVar == 'sample') {
                    var e = document.getElementById('p-sample');
                    if (!!e && e.scrollIntoView) {
                        e.scrollIntoView();
                    }
                }

                $('#samples_table').DataTable({
                    "paging": false,
                    "ordering": true,
                    "info": false,
                    "searching": false,
                    "lengthChange": false,
                    "pageLength": <?= $sampleDataProvider->getPagination()->getPageSize() ?>,
                    "pagingType": "simple_numbers",
                    "columns": [{
                            "visible": <?= in_array('name', $columns) ? 'true' : 'false' ?>
                        },
                        {
                            "visible": <?= in_array('common_name', $columns) ? 'true' : 'false' ?>
                        },
                        {
                            "visible": <?= in_array('scientific_name', $columns) ? 'true' : 'false' ?>
                        },
                        {
                            "visible": <?= in_array('attribute', $columns) ? 'true' : 'false' ?>
                        },
                        {
                            "visible": <?= in_array('taxonomic_id', $columns) ? 'true' : 'false' ?>
                        },
                        {
                            "visible": <?= in_array('genbank_name', $columns) ? 'true' : 'false' ?>
                        },
                    ]
                });


        $('#files_table').DataTable({
            "initComplete": function () {
                $("#files_table").wrap("<div class='dataset-datatables-wrapper'></div>");
            },
            "paging":   false,
            "ordering": true,
            "info":     false,
            "searching": false,
            "lengthChange": false,
            "pageLength": <?=$fileDataProvider->getPagination()->getPageSize() ?>,
            "pagingType": "simple_numbers",
            "columns": [
                { "visible": <?= in_array('name', $setting) ? 'true' : 'false' ?> },
                { "visible": <?= in_array('description', $setting) ? 'true' : 'false' ?> },
                { "visible": <?= in_array('sample_id', $setting) ? 'true' : 'false' ?> },
                { "visible": <?= in_array('type_id', $setting) ? 'true' : 'false' ?> },
                { "visible": <?= in_array('format_id', $setting) ? 'true' : 'false' ?> },
                { "visible": <?= in_array('size', $setting) ? 'true' : 'false' ?> },
                { "visible": <?= in_array('date_stamp', $setting) ? 'true' : 'false' ?> },
                { "visible": <?= in_array('attribute', $setting) ? 'true' : 'false' ?> },
                { "visible": <?= in_array('location', $setting) ? 'true' : 'false' ?> },
              ]
        } );

            });
            /* ----------------------------------- */

            $(".hint").tooltip({
                'placement': 'right'
            });
            $(".image-hint").tooltip({
                'placement': 'top'
            });

            $("#js-expand-btn").click(function() {
                $(this).hide();
                $("#js-close-btn").show();
                $("#js-logs-2").show();
            });

            $("#js-close-btn").click(function() {
                $(this).hide();
                $("#js-expand-btn").show();
                $("#js-logs-2").hide();
            });


            $("#js-expand-btn1").click(function() {
                $(this).hide();
                $("#js-close-btn1").show();
                $("#js-logs-1").show();
            });

            $("#js-close-btn1").click(function() {
                $(this).hide();
                $("#js-expand-btn1").show();
                $("#js-logs-1").hide();
            });

            $("#js-expand-btn2").click(function() {
                $(this).hide();
                $("#js-close-btn2").show();
                $("#js-logs-2").show();
            });

            $("#js-close-btn2").click(function() {
                $(this).hide();
                $("#js-expand-btn2").show();
                $("#js-logs-2").hide();
            });
            $("#js-expand-btn3").click(function() {
                $(this).hide();
                $("#js-close-btn3").show();
                $("#js-logs-3").show();
            });

            $("#js-close-btn3").click(function() {
                $(this).hide();
                $("#js-expand-btn3").show();
                $("#js-logs-3").hide();
            });

            $(".js-download-count").click(function() {
                var location = $(this).attr('href');
                $.ajax({
                    type: 'POST',
                    url: '/adminFile/downloadCount',
                    data: {
                        'file_href': location
                    },
                    success: function(response) {
                        if (response.success) {} else {
                            alert(response.message);
                        }
                    },
                    error: function() {}
                });
            });

            $(".content-popup").popover({
                'placement': 'right'
            });
            $(".citation-popup").popover({
                placement: 'top',
                trigger: 'hover'
            });
        });
    </script>
    <script src="https://hypothes.is/embed.js" async></script>
    <script           >
        document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded
            $(".js-desc").click(function(e) {
                e.preventDefault();
                id = $(this).attr('data');
                const isExpanded = $(this).attr('aria-expanded') === 'true';
                $(this).text(isExpanded ? '+' : '-');
                $(this).attr('aria-label', isExpanded ? 'Show more' : 'Show less');
                $(this).attr('aria-expanded', !isExpanded);

                $('.js-short-' + id).toggle();
                $('.js-long-' + id).toggle();
            });

            $('#myModal').on('hidden.bs.modal', function() {
                $("#message").removeAttr("class").empty();
                $("#advice").removeAttr("class").empty();
            });

            $('#cancel_button').on('click', function() {
                jQuery.ajax({
                    'type': 'GET',
                    'dataType': 'json',
                    'success': function(output) {
                        document.getElementById("message").removeAttribute("class");
                        $("#cancel_button").toggleClass("disable");
                        if (output.status) {
                            $("#message").addClass("alert").addClass("alert-success");
                            $("#message").html(output.message);
                        } else {
                            $("#message").addClass("alert").addClass("alert-error");
                            $("#message").html(output.message);
                        }
                        $("#advice").addClass("alert").addClass("alert-info");
                        $("#advice").empty().append("<a data-dismiss=\"modal\" href=\"#\">You can close this box now.</a>");
                    },
                    'url': '\x2FuserCommand\x2FcancelClaim',
                    'cache': false,
                    'data': jQuery(this).parents("form").serialize()
                });
                return false;
            });

            $('.js-claim-button').on('click', function(event) {
                var author_id = this.getAttribute("data-author-id");
                jQuery.ajax({
                    'type': 'GET',
                    'data': {
                        'dataset_id': $("#dataset_id").val(),
                        'author_id': author_id
                    },
                    'dataType': 'json',
                    'success': function(output) {
                        document.getElementById("message").removeAttribute("class");
                        $("#claim_button").toggleClass("disable");
                        if (output.status) {
                            $("#message").addClass("alert").addClass("alert-success");
                            $("#message").html(output.message);
                        } else {
                            $("#message").addClass("alert").addClass("alert-error");
                            $("#message").html(output.message);
                        }
                        $("#advice").addClass("alert").addClass("alert-info");
                        $("#advice").empty().append("<a data-dismiss=\"modal\" href=\"#\">You can close this box now.</a>");
                    },
                    'url': '\x2FuserCommand\x2Fclaim',
                    'cache': false
                });
                return false;
            });


        });
    </script>
    <script>
      function handleInitFilesPage() {
          let currentPageNumber = 1;

          const match = window.location.pathname.match(/Files_page\/(\d+)/);
          if (match && match[1]) {
              currentPageNumber = parseInt(match[1], 10);
          }

          $('#pageNumber').val(currentPageNumber);
      }
      function handleInitSamplesPage() {
          let currentPageNumber = 1;

          const match = window.location.pathname.match(/Samples_page\/(\d+)/);
          if (match && match[1]) {
              currentPageNumber = parseInt(match[1], 10);
          }

          $('#pageNumber').val(currentPageNumber);
      }
      function handleInitPagination() {
          let currentPageNumber = 1;

          const match = window.location.pathname.match(/Files_page\/(\d+)/);
          if (match && match[1]) {
              currentPageNumber = parseInt(match[1], 10);
          }

          $('#pageNumber').val(currentPageNumber);
      }

      function isNumber(value) {
        return typeof value === 'number' && !isNaN(value) && isFinite(value);
      }


      function computePageNumber(userInput, min, max) {
        const [_userInput, _min, _max] = [parseInt(userInput), parseInt(min), parseInt(max)];

        if (![_userInput, _min, _max].every(isNumber)) {
          console.error("computePageNumber: Invalid input")
          return _min; // Default fallback to minimum page
        }

        if (_userInput >= _min && _userInput <= _max) {
          console.log("Valid page number!");
          return _userInput;
        } else if (_userInput > _max) {
          console.log("Error, return to " + _max);
          return _max;
        } else if (_userInput < _min) {
          console.log("Error, return to " + _min);
          return _min;
        }
      }

      function goToFilesPage() {
        const pageID = <?php echo $model->identifier ?>;
        //To validate page number
        const max = <?php echo $fileDataProvider->getPagination()->getPageCount() ?>;
        const min = 1;
        let targetPageNumber = document.getElementById("filesPageInput").value;
        const userInput = parseInt(targetPageNumber);
        const targetUrlArray = ["", "dataset", "view", "id", pageID];

        targetUrlArray.push('Files_page', computePageNumber(userInput, min, max));
        window.location = window.location.origin + targetUrlArray.join("/");
      }

      function goToSamplesPage() {
        const pageID = <?php echo $model->identifier ?>;
        //To validate page number
        const max = <?php echo $sampleDataProvider->getPagination()->getPageCount() ?>;
        const min = 1;
        let targetPageNumber = document.getElementById("samplesPageInput").value;
        const userInput = parseInt(targetPageNumber);
        const targetUrlArray = ["", "dataset", "view", "id", pageID];

        targetUrlArray.push('Samples_page', computePageNumber(userInput, min, max));
        window.location = window.location.origin + targetUrlArray.join("/");
      }

      function detectEnterKeyPress(event) {
        const validIds = ["filesPageInput", "samplesPageInput"];
        const id = event.target.id;

        if (!validIds.includes(id)) {
          return
        }

        if (event.which === 13 || event.keyCode === 13 || event.key === "Enter") {
          id === "filesPageInput" ? goToFilesPage() : goToSamplesPage();
        }
      }

      function handlePaginationCssClasses() {
        $("ul.yiiPager li.first-visible").removeClass("first-visible");
        $("ul.yiiPager li:not(.hidden)").first().addClass("first-visible");
        $("ul.yiiPager li.last-visible").removeClass("last-visible");
        $("ul.yiiPager li:not(.hidden)").last().addClass("last-visible");
      }

      $(document).ready(function() {
        handleInitPagination()
        handlePaginationCssClasses()
      });

    </script>