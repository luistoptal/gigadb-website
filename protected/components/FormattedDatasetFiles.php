<?php

/**
 * Adapter class to present files associated to a dataset for a dataset view
 *
 * @param DatasetFilesInterface $datasetFiles the adaptee class to fall back to
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetFiles extends DatasetComponents implements DatasetFilesInterface
{
    private DatasetFilesInterface $_cachedDatasetFiles;
    private CPagination $pager;

    public function __construct(CPagination $pager, DatasetFilesInterface $datasetFiles)
    {
        parent::__construct();
        $this->_cachedDatasetFiles = $datasetFiles;
        $this->pager = $pager;
    }

    /**
     * return the dataset id
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_cachedDatasetFiles->getDatasetId();
    }

    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return  $this->_cachedDatasetFiles->getDatasetDOI();
    }

    /**
     * retrieve files associated to a dataset
     *
     * @return array of external link array map
     */
    public function getDatasetFiles(?string $limit = "ALL", ?int $offset = 0): array
    {
        $files =   $this->_cachedDatasetFiles->getDatasetFiles($limit, $offset);
        foreach ($files as &$file) {
            $file['nameHtml'] = "<div title=\"" . $file['description'] . "\"><a href=\"" . $file['location'] . "\" target='_blank'>" . $file['name'] . "</a></div>";
            $file['sizeUnit'] = UnitHelper::specifySizeUnits($file['size']);
            $attribute_strings = [];
            foreach ($file['file_attributes'] as $file_attribute) {
                $attribute_strings[] = implode(array_keys($file_attribute)) . ": " . implode(array_values($file_attribute)) . "<br>";
            }
            $file['attrDesc'] = implode('', $attribute_strings);
        }
        return $files;
    }

    /**
     * count number of files associated to a dataset
     *
     * @return int how many files are associated with the dataset
     */
    public function countDatasetFiles(): int
    {
        return $this->_cachedDatasetFiles->countDatasetFiles();
    }

    /**
     * retrieve, cache and format the sample attached to files associated to a dataset
     *
     * @return array of files array maps
     */
    public function getDatasetFilesSamples(): array
    {
        return $this->_cachedDatasetFiles->getDatasetFilesSamples();
    }

    /**
     * Wrap the list of files associated to a dataset in a CArrayProvider so we get pagination and sorting
     *
     * @return CArrayDataProvider the DataProvider to be usd in view templates for managing the list of files to display
     */
    public function getDataProvider(): CArrayDataProvider
    {

        $totalFileCount = $this->countDatasetFiles() ;
        $this->pager->setItemCount($totalFileCount);
        $this->pager->pageVar = "Files_page";

        $currentPage = $this->pager->getCurrentPage();
        $nbToSkip = $currentPage*$this->pager->getPageSize();


        $files = $this->getDatasetFiles($this->pager->getPageSize(), $nbToSkip);
        if (defined('YII_DEBUG') && true === YII_DEBUG) {
            Yii::log("Current page: $currentPage", 'info');
            Yii::log("nb file returned: " . count($files), 'info');
        }

        $dataProvider = new CArrayDataProvider(null, array(
            'totalItemCount' => $totalFileCount,
            'sort' => array('defaultOrder' => 'name ASC',
                            'attributes' => array(
                                'name',
                                'description',
                                'size',
                                'type',
                                'format',
                                'date_stamp'
                            )
                        ),
            'pagination' => null
            ));
        $dataProvider->setPagination($this->pager);
        $dataProvider->setData($files);
        if (defined('YII_DEBUG') && true === YII_DEBUG) {
            Yii::log("Item count: " . $dataProvider->getItemCount(), "info");
            Yii::log("Total count: " . $dataProvider->getTotalItemCount(), "info");
        }
        return $dataProvider;
    }

    /**
     * Format the sample list to show up to 3 of them and a + button if more
     *
     * @param int num maximum number of sample item to display before showing the + button
     * @param int $selection if supplied, filter the result array to a specific file, otherwise returns samples for all files
     * @return string html snippet for the Sample column of file tab
     */
    public function formatDatasetFilesSamples(int $nb_item, int $selection = null): array
    {
        $formatted_files = [];

        // callback function to list all sample names, comma separated, as a string. To be used in array_reduce
        $toListString  = function ($list, $sample) {
            return $list .= ", " . $sample['sample_name'];
        };


        // loop over all files for the dataset
        $files = $this->getDatasetFiles() ;
        // filter to select file if selection supplied
        if (!empty($selection)) {
            $files = array_values(array_filter($files, function ($file) use ($selection) {
                if ($selection == $file['id']) {
                    return true;
                }
                return false;
            }));
        }
        foreach ($files as $file) {
            $file_id = $file['id'];

            // callback function to filter all the samples for all files for the dataset by file id. To be used in array_filter
            $selectFile = function ($sample) use ($file_id) {
                if ($file_id == $sample['file_id']) {
                    return true;
                }
                return false;
            };

            // get the samples for the selected file
            $samples_for_file = array_values(array_filter($this->getDatasetFilesSamples(), $selectFile)) ;

            // based on the number of samples for that files we display all them or only one with a "+" button to trigger the display of them all
            $visible = '' ;
            $hidden = '' ;
            $more_link = '' ;
            if (count($samples_for_file) <= $nb_item) {
                $visible = "<span class=\"js-short-${file_id}\">" . ltrim(array_reduce($samples_for_file, $toListString), ", ") . "</span>";
            } else {
                $visible = "<span class=\"js-short-${file_id}\">" . ltrim(array_reduce(array_slice($samples_for_file, 0, 1), $toListString), ", ") . "</span>";
                $hidden = "<span class=\"js-long-${file_id}\" style=\"display: none;\">" . ltrim(array_reduce($samples_for_file, $toListString), ", ") . "</span>";
                $more_link = "<a href=\"#\" class=\"js-desc\" data=\"${file_id}\">+</a>";
            }

            // push into the resulting array
            $formatted_files[] = array('file_id' => $file_id, 'visible' => $visible, 'hidden' => $hidden, 'more_link' => $more_link);
        }

        return $formatted_files;
    }
}
