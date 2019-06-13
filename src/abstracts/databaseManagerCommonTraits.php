<?php
namespace carlonicora\minimalism\abstracts;

trait databaseManagerCommonTraits
{
    /**
     * @param $id
     * @return array|null
     */
    public function loadFromId($id){
        $sql = $this->generateSelectStatement();
        $parameters = $this->generateSelectParameters();

        $parameters[1] = $id;

        $response = $this->runReadSingle($sql, $parameters);

        return($response);
    }
}