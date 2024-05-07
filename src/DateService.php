<?php

namespace Utilita;

class DateService
{

    /*
     * Converte in seriale una data
     */
    public static function datatotime($data, $frm = NULL)
    {
        if ($data == '' || $data == '--') {
            //$time = "ERRORE NESSUNA DATA PASSA";
            return;
        } else {
            if ($frm = 'EN') {
                list($y, $m, $d) = preg_split('/[-\/\\\.,]/', $data);
            } else {
                list($d, $m, $y) = preg_split('/[-\/\\\.,]/', $data);
            }
        }
        //return $time;
        return mktime(0, 0, 0, $m, $d, $y);
    }

    /*
     * converte in mesi una data seriale
     *
     */
    public static function getmese($serial = null)
    {
        $serial = $serial / (86400 * 30);
        return $serial;
    }

    /*
     * converte in giorni una data seriale
     *
     */
    public static function getgiorni($serial = null)
    {
        $serial = ceil($serial / 86400);
        return $serial;
    }

    /**
     * Return italia mese from date
     *
     * @param mixed $data
     * @return mixed
     */
    public static function _getMese($data)
    {
        $mesi = [
            ['id' => '1', 'fid' => '01', 'it' => 'gennaio',],
            ['id' => '2', 'fid' => '02', 'it' => 'febbraio',],
            ['id' => '3', 'fid' => '03', 'it' => 'marzo',],
            ['id' => '4', 'fid' => '04', 'it' => 'aprile',],
            ['id' => '5', 'fid' => '05', 'it' => 'maggio',],
            ['id' => '6', 'fid' => '06', 'it' => 'giugno',],
            ['id' => '7', 'fid' => '07', 'it' => 'luglio',],
            ['id' => '8', 'fid' => '08', 'it' => 'agosto',],
            ['id' => '9', 'fid' => '09', 'it' => 'settembre',],
            ['id' => '10', 'fid' => '10', 'it' => 'ottobre',],
            ['id' => '11', 'fid' => '11', 'it' => 'novembre',],
            ['id' => '12', 'fid' => '12', 'it' => 'dicembre',],
        ];
        $mese = date('m', strtotime($data));
        $return = array_search($mese, array_column($mesi, 'fid'));
        return $mesi[$return]['it'];
    }

    /*
     * converte data seriale in formato italiano
     */
    public static function _getData($time = NULL)
    {
        $data = ($time > 0) ? getdate($time) : NULL;
        $sep = "-";
        if (strlen($data['mon']) == '1') {
            $mese = "0$data[mon]";
        } else {
            $mese = $data['mon'];
        }
        if (strlen($data['mday']) == '1') {
            $giorno = "0$data[mday]";
        } else {
            $giorno = $data['mday'];
        }
        $datatime = $giorno . $sep . $mese . $sep . $data['year'];
        return $datatime;
    }

    /*
     * Converte in seriale una data
     */
    public static function _DataIT($data)
    {
        if ($data == '' || $data == '--') {
            //$time = "ERRORE NESSUNA DATA PASSA";
            return;
        } else {
            list($y, $m, $d) = preg_split('/[-\/\\\.,]/', $data);
            $datait = $d . '-' . $m . '-' . $y;
        }
        return $datait;
    }

    /*
     * Converte in seriale una data
     */
    public static function _DataToTime($data, $frm = NULL)
    {
        if ($data == '' || $data == '--') {
            //$time = "ERRORE NESSUNA DATA PASSA";
            return;
        } else {
            if ($frm = 'EN') {
                list($y, $m, $d) = preg_split('/[-\/\\\.,]/', $data);
            } else {
                list($d, $m, $y) = preg_split('/[-\/\\\.,]/', $data);
            }
        }
        //return $time;
        return mktime(0, 0, 0, $m, $d, $y);
    }

    /*
     * converte in mesi una data seriale
     *
     */
    public static function _getMeseBySerial($serial = null)
    {
        $serial = $serial / (86400 * 30);
        return $serial;
    }


    /*
     * converte in giorni una data seriale
     *
     */
    public static function _GetGiorni($serial = null)
    {
        $serial = ceil($serial / 86400);
        return $serial;
    }

    /*
     * Converte una data nel formato che si vuole
     * ex datato('20/10/2000', 'EN')
     * restituisce Y/M/D
     * 2000/10/20
     */
    public static function _Datato($data = NULL, $to = NULL, $sep = NULL)
    {
        if (is_null($data) || is_null($to) || is_null($sep)) {
            //$time = "ERRORE NESSUNA DATA PASSA";
            return;
        } else {
            list($a, $b, $c) = preg_split('/[-\/\\\.,]/', $data);
            if (strlen($a) > 2) {
                $y = $a;
                $m = $b;
                $d = $c;
            } elseif (strlen($a) <= 2) {
                $y = $c;
                $m = $b;
                $d = $a;
            } else {
                return;
            }
            switch (strtoupper($to)) {
                case 'IT':
                    $trf = $d . $sep . $m . $sep . $y;
                    break;
                case 'EN':
                    $trf = $y . $sep . $m . $sep . $d;
                    break;
            }
        }
        return $trf;
    }
}
