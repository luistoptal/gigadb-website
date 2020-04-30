<?php
/**
 * This action will load the metadata form
 *
 * URL: /authorisedDataset/filesAnnotate/100006
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */

use Yii;
use \yii\web\UploadedFile;

class FilesAnnotateAction extends CAction
{

    public function run($id)
    {
        $this->getController()->layout='uploader_layout';
        $webClient = new \GuzzleHttp\Client();

        // Instantiate FileUploadService and DatasetUpload
        $fileUploadSrv = new FileUploadService([
            "tokenSrv" => new TokenService([
                                  'jwtTTL' => 3600,
                                  'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                                  'jwtSigner' => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                                  'users' => new UserDAO(),
                                  'dt' => new DateTime(),
                                ]),
            "webClient" => $webClient,
            "requester" => Yii::app()->user,
            "identifier"=> $id,
            "dataset" => new DatasetDAO(["identifier" => $id]),
            "dryRunMode"=>false,
            ]);

        $datasetUpload = new DatasetUpload(
            $fileUploadSrv->dataset, 
            $fileUploadSrv, 
            Yii::$app->params['dataset_upload']
        );
        // Fetch list of uploaded files
        $uploadedFiles = $fileUploadSrv->getUploads($id);

        // Fetch list of attributes
        $attributes = [];
        foreach($uploadedFiles as $upload) {
            $attributes[$upload['id']] = $fileUploadSrv->getAttributes($upload['id']);
        }

        $userMessage = "";
        $bulkStatus = false;
        $bulkAttrStatus = false;
        $parseErrors = [] ;
        $mergeErrors = [] ;
        if(isset($_FILES) && is_array($_FILES) && isset($_FILES["bulkmetadata"])) {
            $postedFile = UploadedFile::getInstanceByName("bulkmetadata");
            $postedFile->saveAs("/var/tmp/$id-".$postedFile->name);
            list($sheetData, $parseErrors) = $datasetUpload->parseFromSpreadsheet("/var/tmp/$id-".$postedFile->name);
            if (isset($sheetData) && is_array($sheetData) && !empty($sheetData)) {
                list($newUploads, $attributes, $mergeErrors) = $datasetUpload->mergeMetadata($uploadedFiles, $sheetData);
                $bulkStatus = $fileUploadSrv->updateUploadMultiple($id,$newUploads);
                Yii::log("Parsed attributes: ".var_export($attributes, true),'info');
                $bulkAttrStatus = $fileUploadSrv->addAttributes($id,$attributes);
                Yii::log("update Upload Multiple: ", $bulkStatus);
            }
            if($bulkStatus) {
                Yii::app()->user->setFlash('filesAnnotate','Metadata loaded');
            }
            if(count(array_merge($parseErrors, $mergeErrors)) > 0 ) {
                 Yii::app()->user->setFlash('filesAnnotateErrors',implode("\n",array_merge($parseErrors, $mergeErrors)));
            }

            $this->getController()->redirect(["authorisedDataset/annotateFiles", "id" => $id]);            
        }

        if(isset($_POST['DeleteList']))
        {
            $deletedlist = $fileUploadSrv->deleteUploads($_POST['DeleteList']);
            if(count($deletedlist)>0) {
                $userMessage .= count($deletedlist)." File(s) successfully deleted<br>";
            }

        }

        $allUploadsSaved = true;
        if(isset($_POST['Upload']))
        {
            foreach($uploadedFiles as $upload)
            {
                if(isset($_POST['Upload'][$upload['id']])) {
                    $allUploadsSaved = $allUploadsSaved && $fileUploadSrv->updateUpload($upload['id'], $_POST['Upload'][$upload['id']] );
                }
            }
        }

        $allAttributesSaved = true;
        if(isset($_POST['Attributes']))
        {
            foreach($uploadedFiles as $upload)
            {
                if(isset($_POST['Attributes'][$upload['id']])) {
                    $attributes = $_POST['Attributes'][$upload['id']];
                    $allAttributesSaved = $allAttributesSaved && $fileUploadSrv->setAttributes($upload['id'], $attributes );
                    if ($allAttributesSaved) {
                        $userMessage .= count($attributes)." attribute(s) added for upload ".$upload['id']."<br>";
                    }
                }
            }
        }

        if (Yii::$app->request->isPost && $allUploadsSaved && $allAttributesSaved) {

                $statusChangedAndNotified = $datasetUpload->setStatusToDataAvailableForReview(
                    $datasetUpload->renderNotificationEmailBody(
                        "DataAvailableForReview"
                    )
                );
                if($statusChangedAndNotified) {
                    $userMessage .= "File uploading complete<br>";
                    Yii::app()->user->setFlash('fileUpload', $userMessage);
                    $this->getController()->redirect("/user/view_profile#submitted");
                }
                else {
                    Yii::app()->user->setFlash('error','Error changing and notifying dataset upload status');
                }
                
        }
        elseif ( Yii::$app->request->isPost ) {
                Yii::app()->user->setFlash('error','Error with some files');
        }
        $this->getController()->render("filesAnnotate", array("identifier" => $id, "uploads" => $uploadedFiles, "attributes" => $attributes));
    }
}

?>