<?php

namespace src\model;

require_once("Database.php");

use \DateTimeImmutable;
use \DateInterval;
use \DateTimeZone;
use PDOException;
use \scr\model\Database;

class shortUrl {

    /**
     * Nome da tabela no banco de dados
     * @var String
     */
    const TABLE_NAME = 'urlactive';

    /**
     * Tamanho dos links encurtados
     * @var integer
     */
    const SHORTED_LINK_LENGHT = 5;

    /**
     * Periodo que o link ficará ativo na notação da classe DateInterval;
     * @var String
     */
    const LINK_PERIOD_ACTIVE = 'P1D';
    
    /**
     * Url original que irá ser encurtada
     * @var String
     */
    private String $url;

    /** 
     * Nova url encurtada
     * @var String
    */
    private String $shortUrl;

    /**
     * Data e Hora que a url foi encurtada
     * @var DateTimeImmutable
     */
    private DateTimeImmutable $created_at;

    /**
     * Data e Hora que a url expira e irá deixar de ser acessível
     * @var String
     */
    private String $expired_at;

    /**
     * Constroi o link curto junto de seus metadados
     * @param String $url
     */
    public function __construct(String $url = null)
    { 
        
        if (!is_null($url)) {
            $modified_url = self::formatURL($url);
        
            $this->url = $url;
            $this->shortUrl = self::generateShortURL($modified_url);        
            $this->created_at = self::getActualTime();
            $this->expired_at = self::getExpirationTime($this->created_at)->format("Y-m-d H:i:s");                
        }
    }    

    /**
     * Método responsável por encurtar a URL
     * @param String $url
     * @return String
     */
    public static function generateShortURL(String $url) 
    {
        $shortedUrl = "";
        while (strlen($shortedUrl) != self::SHORTED_LINK_LENGHT) {

            $letter_or_number =  rand(0, 1); // 0 = letra e 1 = número
        
            if ($letter_or_number == 0) {
                $url_length =  strlen($url) - 1;
                $randomCaracter_From_String = rand(0, $url_length);
                $shortedUrl = $shortedUrl.$url[$randomCaracter_From_String];
            } else {
                $randomNumber = rand(0,9);
                $shortedUrl = $shortedUrl.$randomNumber;
            }
    }    

        return $shortedUrl;
    }

    /**
     * Retorna uma URL sem seus caracters especiais: (.), (/), (?), (=)
     * @param String $url
     * @return String
     */
    private static function formatURL($url) 
    {
        $modified_url = str_replace([".", "/", "?", "="], "", $url);    
        return $modified_url;
    }
    
    /**
     * Retorna a data e a hora da criação da URL configurada para o TimeZone "America/Sao_Paulo"
     * @return DateTimeImmutable
     */
    private static function getActualTime() 
    {
        $timeZone = new DateTimeZone('America/Sao_Paulo');
        return new DateTimeImmutable('now', $timeZone);
    }

    /**
     * Retorna da data de expiração de um link dado a sua data de criação
     * @param DateTimeImmutable $date
     * @return DateTimeImmutable
     */
    private static function getExpirationTime(DateTimeImmutable $date)
    {
        $dateInterval = new DateInterval(self::LINK_PERIOD_ACTIVE);
        return $date->add($dateInterval);
    }

    /**
     * Retorna a URL encurtada
     * @return String
     */
    public function getShortURL()
    {
        return $this->shortUrl;
    }

    /**
     * Retorna a URL original
     * @return String
     */
    public function getURL() 
    {
        return $this->url;    
    }

    /**
     * Retorna a data e hora de criação da url encurtada
     * @return String 
     */
    public function getCreated_At() 
    {
        $dateTime = $this->created_at->format('Y-m-d H:i:s');
        return $dateTime;        
    }

    /**
     * Retorna a data e hora da expiração da url
     * @return String 
     */
    public function getExpired_At()
    {
        $dateTime = $this->expired_at;
        return $dateTime;
    }    
    
    /**
     * Insere os dados de uma shortUrl no banco de dados
     * @var shortUrl $url
     * @return Boolean
     */
    public static function insert(shortUrl $url)
    {
        $connection = new Database(self::TABLE_NAME);
        $fields = [
            'shortUrl' => $url->getShortURL(),
            'url' => $url->getURL(),
            'created_at' => $url->getCreated_At(),
            'expired_at' => $url->getExpired_At()
        ];

        $id = $connection->insert($fields);
        return true;
    }

    /**
     * Retorna uma instância de uma shortUrl no banco de dados
     * @param String $url
     * @return shortUrl
     */
    public static function selectShortUrl(String $url) 
    {                
        $column = 'shortUrl';
        $fields = [
            'shortUrl',
            'url',
            'expired_at'            
        ];

        $connection = new Database(self::TABLE_NAME);        
        $result = $connection->selectWhere($column, $url, $fields)->fetchObject(self::class);                        
        return $result;        
    }
}