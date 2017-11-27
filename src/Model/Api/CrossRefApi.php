<?php
/*
 * This file is part of the Bibliometric Snowballing project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace BS\Model\Api;


use BS\Model\Entity\Work;

class CrossRefApi extends AbstractApi
{
    /**
     * Returns DOI information by requesting the API.
     *
     * @param string $doi DOI
     * @return array API data
     */
    public function getDoiInformation($doi)
    {
        return json_decode($this->requestWorkByDoi($doi), true);
    }

    /**
     * Requests the API and returns the raw data.
     *
     * @param string $doi DOI
     * @return string API data
     */
    protected function requestWorkByDoi($doi)
    {
        return parent::request(str_replace('{doi}', $doi, $this->configuredUrl));
    }
}
