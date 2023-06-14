<?php
namespace Qandacc\AmazonTracking;

/**
 * AmazonTrackingClient 
 *
 * @author Pietro Celeste <pietro.celeste@gmail.com>
 */
class Client
{
    const API_ENDPOINT = 'https://track.amazon.it/api/tracker/';

    /**
     * Metodo main che processa la costruzione della history del tracking
     *
     * @param string $trackingCode
     * @return array
     */
    public function getTrackingHistory($trackingCode)
    {
        $this->validateTrackingCode($trackingCode);
        $url = $this->remoteCallUrlFactory($trackingCode);
        $amazonResponse = $this->getRawTracking($url);
        $amazoneResponseArray = $this->rawRespons2Array($amazonResponse);
        return $this->amazonResponseArray2TrackingHistory($amazoneResponseArray);
    }

    /**
     * Effettua una validazione del codice di tracking passato come argomento
     *
     * @param string $trackingCode Codice di tracking della spedizione
     */
    protected function validateTrackingCode($trackingCode)
    {
        if (empty($trackingCode)) {
            $this->raiseException('Il parametro trackingCode è vuoto');
        }
    }

    /**
     * Costruisce la url da richiamare per ottenere da Amazon
     * il tracking collegato al codice passato come input
     *
     * @param string $trackingCode codice di tracking della spedizione
     * @return string
     */
    protected function remoteCallUrlFactory($trackingCode)
    {
        return self::API_ENDPOINT . $trackingCode;
    }

    /**
     * Metodo che effettua la chiamata remota a restituisce il json in formato stringa
     *
     * @param string $url da richiamare per ottenere il tracking Amazon grezzo
     * @return string
     */
    protected function getRawTracking($url)
    {
        return file_get_contents($url);
    }

    /**
     * Trasformo la response (string) ottenuta dala chiamata remota in array
     *
     * @param type $amazonResponse
     * @return type
     */
    protected function rawRespons2Array($amazonResponse)
    {
        $rawJson = json_decode($amazonResponse, true);
        if ($rawJson === false) {
            $this->raiseException('Amazon response is invalid');
        }
        if (!array_key_exists('eventHistory', $rawJson)) {
            $this->raiseException('eventInstory element is not present');
        }        
        if (empty($rawJson['eventHistory'])) {
            $this->raiseException($rawJson['progressTracker'] ?? 'Errore nella ripresa dati remota');
        }
        return json_decode($rawJson['eventHistory'], true)['eventHistory'];
    }

    /**
     * Formatto la history ottenuta dalla chiamata remota in array come da specifiche
     * qapla
     *
     * @param array $amazonTrackingHistory
     * @return array
     */
    protected function amazonResponseArray2TrackingHistory($amazonTrackingHistory)
    {        
        return array_map(
            function($row) {
                return [
                'event' => $row['eventCode'],
                'datetime' => $row['eventTime'],
                'location' => implode(', ', $row['location'])
                ];
            },
            $amazonTrackingHistory
        );
    }

    /**
     * Metodo che solleva un'exception
     *
     * @param string $message
     * @throws \Exception
     */
    protected function raiseException($message)
    {
        throw new \Exception($message);
    }
}
