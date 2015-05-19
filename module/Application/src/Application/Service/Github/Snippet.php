<?php
namespace Application\Service\Github;

class Snippet
{
    const MAX_LOC    = 25;
    const MAX_LENGTH = 50;

    const VERTICAL   = 'vertical';
    const HORIZONTAL = 'horizontal';

    protected $language;
    protected $code;

    public function __construct($language, $code)
    {
        $this->language = $language;
        if(!is_array($code)){
            $code = explode("\n", $code);
        }

        //todo: remove whitespace
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return array
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getLoc()
    {
        return count($this->code);
    }

    public function getLongest()
    {
        $longest = 0;
        foreach($this->code as $line){
            if(strlen($line) > $longest){
                $longest = strlen($line);
            }
        }

        return $longest;
    }

    public function isOk()
    {
        if($this->getLoc() > self::MAX_LOC){
            return false;
        }

        if($this->getLongest() > self::MAX_LENGTH){
            return false;
        }

        return true;
    }

    public function getDirection()
    {
        return self::HORIZONTAL;
    }

}