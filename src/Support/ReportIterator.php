<?php namespace LabtechSoftware\ConnectwisePsaSdk\Support;

class ReportIterator implements \Iterator
{
    private $position = 0;
    private $array = array();

    public function __construct($reportResult, $keyValueOnly = false)
    {
        // Throw an error if we didnt get a valid result
        if (!property_exists($reportResult, 'RunReportQueryResult')) {
            throw new ApiException('Report result passed not valid');
        }


        // Only do all this if there are results
        if (property_exists($reportResult->RunReportQueryResult, 'ResultRow')) {

            // Lets get a starting point. Thanks for the massive object XML :P
            $reportResult = $reportResult->RunReportQueryResult->ResultRow;

            // if its an object then it means there was one result. Lets make it an array for consistency
            if (is_object($reportResult)) {
                $reportResult = array($reportResult);
            }

            // Do we want a simplified result set with just the property name and value?
            if ($keyValueOnly === true) {
                $reportResult = $this->convertToKeyValue($reportResult);
            }

            // Now we set the array to the results for later iteration
            $this->array = $reportResult;
        }
    }

    /**
     * Converts the results array into a cleaner array of objects.
     *
     * @param array $reportResult
     * @return array
     */
    private function convertToKeyValue($reportResult)
    {
        $items = array();
        foreach ($reportResult as $item) {

            $tmpItems = new \stdClass();
            foreach ($item->Value as $v) {
                $tmpItems->{$v->Name} = $v->_;
            }
            $items[] = $tmpItems;
        }
        return $items;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->array[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->array[$this->position]);
    }
}
