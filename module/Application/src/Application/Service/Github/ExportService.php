<?php
namespace Application\Service\Github;

use Application\Service\HasHttpClient;
use Zend\Http\Client;
use Zend\Uri\Http as Uri;

/**
 * Simple service to get some lines of code from GitHub.
 * @package Application\Service\Github
 */
class ExportService
{
    use HasHttpClient;

    /**
     * @param $uri
     * @return Snippet
     */
    public function getSnippet($uri)
    {
        //Set things up to manipulate the URL
        if(!($uri instanceof Uri)){
            $uri = new Uri($uri);
        }

        //Only wrk with github
        if($uri->getHost() != 'github.com'){
            throw new \UnexpectedValueException('url not from github');
        }

        //Expect a range of lines
        $range = [];
        $lines = $uri->getFragment();

        if(!empty($lines)){
            $range = explode('-', $lines);
            foreach($range as $index => $line){
                $line = trim($line, 'L');
                $range[$index] = $line;
            }
        }

        //Don't use this yet, but could
        $type = pathinfo($uri->getPath())['extension'];

        //Change request to get raw content
        $uri->setHost('raw.githubusercontent.com');
        $uri->setPath(str_replace('/blob/', '/', $uri->getPath()));

        //Get the code
        $response = $this->getHttpClient()->setUri($uri)->send();
        if(!$response->isSuccess()){
            throw new \RuntimeException('could not fetch code');
        }

        $code = $response->getBody();
        $code = explode("\n", $code);

        switch(count($range)){
            case 1:
                return new Snippet($type, $code[--$range[0]]);
            case 2;
                return new Snippet($type, array_slice($code, --$range[0], $range[1] - $range[0]));
            default:
                return new Snippet($type, $code);
        }
    }
}