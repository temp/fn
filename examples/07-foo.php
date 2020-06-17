<?php

include __DIR__ . '/../vendor/autoload.php';

use function Fnc\all;
use function Fnc\always;
use function Fnc\applySpec;
use function Fnc\complement;
use function Fnc\compose;
use function Fnc\converge;
use function Fnc\filter;
use function Fnc\ifElse;
use function Fnc\isEmpty;
use function Fnc\merge;
use function Fnc\pipe;
use function Fnc\prop;

$data = [
    '__Administrative Notizen' => NULL,
    '__Allokation' => ['version' => 1, 'fonds' => ['4801a9f4-d125-4e98-827d-18ed0ca73dcf' => ['vorgeschlagen' => false, 'initialesVotum' => NULL, 'initialesVotumBegruendung' => '', 'zweitesVotum' => NULL, 'zweitesVotumBegruendung' => '',], 'ed048f10-e92d-40bd-a690-9bdb11366758' => ['vorgeschlagen' => true, 'initialesVotum' => true, 'initialesVotumBegruendung' => 'c', 'zweitesVotum' => true, 'zweitesVotumBegruendung' => 'd',], 'f385972f-2426-45ae-ab8a-984606b3c22c' => ['vorgeschlagen' => false, 'initialesVotum' => NULL, 'initialesVotumBegruendung' => '', 'zweitesVotum' => NULL, 'zweitesVotumBegruendung' => '',], '208d43cf-2853-476c-ba48-d42922f03fbe' => ['vorgeschlagen' => false, 'initialesVotum' => NULL, 'initialesVotumBegruendung' => '', 'zweitesVotum' => NULL, 'zweitesVotumBegruendung' => '',], 'b81a188b-9d33-47cc-bbe8-fdeb90e66448' => ['vorgeschlagen' => false, 'initialesVotum' => NULL, 'initialesVotumBegruendung' => '', 'zweitesVotum' => NULL, 'zweitesVotumBegruendung' => '',], '7d13ca7c-97dd-4824-8522-e8f1f86f50fe' => ['vorgeschlagen' => true, 'initialesVotum' => false, 'initialesVotumBegruendung' => 'e', 'zweitesVotum' => true, 'zweitesVotumBegruendung' => 'f',], '40ccdca2-8704-4140-9c93-26e690225c84' => ['vorgeschlagen' => true, 'initialesVotum' => false, 'initialesVotumBegruendung' => 'g', 'zweitesVotum' => false, 'zweitesVotumBegruendung' => 'h',], '446962fb-636e-4623-a466-699fd79093bc' => ['vorgeschlagen' => false, 'initialesVotum' => NULL, 'initialesVotumBegruendung' => '', 'zweitesVotum' => NULL, 'zweitesVotumBegruendung' => '',], '61bd7ffb-6f5b-4cee-a46c-20897b7b1cf1' => ['vorgeschlagen' => true, 'initialesVotum' => true, 'initialesVotumBegruendung' => 'a', 'zweitesVotum' => false, 'zweitesVotumBegruendung' => 'b',],],],
    '__Angebotsdatum' => '2020-06-25',
    '__Anzahl Hotelzimmer' => 5.0,
    '__Anzahl Stellplätze außen' => 3.0,
    '__Anzahl Stellplätze gesamt' => 4.0,
    '__Anzahl Stellplätze innen' => 2.0,
    '__Anzahl der Mieter' => 7000.0,
    '__Baujahr' => 2000.0,
    '__Baujahr-Kommentar' => 'bauj anm',
    '__Bedrohungen' => 'leider auch',
    '__Chance auf Deal-Closing in Prozent' => 4567.0,
    '__Chancen' => 'sind vorhanden',
    '__Datenraumunterlagen' => NULL,
    '__Eigentumsform' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10378', 'value' => 'Erbbaurecht', 'id' => '10378',],
    '__Empfehlung' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10009', 'value' => 'Kaufempfehlung', 'id' => '10009',],
    '__Finaler Kaufpreis' => 1100000.0,
    '__Finaler Kaufpreis in Fremdwährung' => 1200000.0,
    '__Flächeneinheit' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10389', 'value' => 'm²', 'id' => '10389',],
    '__Fonds' => NULL,
    '__Fremdkapitalquote in Prozent' => 3456.0,
    '__Funding' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10386', 'value' => 'Forward Funding', 'id' => '10386',],
    '__Gebühr für die CR in Eurocent' => 2100000.0,
    '__Gebühr für die CR in Prozent' => 5678.0,
    '__Grundstücksgröße' => 100000.0,
    '__Haupt-Bild-Feld' => NULL,
    '__Hauptmieter' => 'die konkurrenz',
    '__Initialer Angebotskaufpreis' => 1900000.0,
    '__Initialer Angebotskaufpreis in Fremdwährung' => 2000000.0,
    '__Investitionskosten pro Quadratmeter in Euro' => 1500000.0,
    '__Investitionskosten pro Quadratmeter in Fremdwährung' => 1600000.0,
    '__Investmentmanager' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/user?username=vvorstand', 'name' => 'vvorstand', 'key' => 'JIRAUSER10107', 'emailAddress' => 'dev-user+vvorstand@seibert-media.net', 'avatarUrls' => ['48x48' => 'http://host.docker.internal:2990/jira/secure/useravatar?ownerId=vvorstand&avatarId=10605', '24x24' => 'http://host.docker.internal:2990/jira/secure/useravatar?size=small&ownerId=vvorstand&avatarId=10605', '16x16' => 'http://host.docker.internal:2990/jira/secure/useravatar?size=xsmall&ownerId=vvorstand&avatarId=10605', '32x32' => 'http://host.docker.internal:2990/jira/secure/useravatar?size=medium&ownerId=vvorstand&avatarId=10605',], 'displayName' => 'Viktor Vorstand', 'active' => true, 'timeZone' => 'Europe/Berlin',],
    '__Ist-Miete p.a.' => 500000.0,
    '__Ist-Miete p.a. in Fremdwährung' => 600000.0,
    '__KV-Abschluss/Datum' => '2020-07-02',
    '__KV-Abschluss/Quartal' => '2020-07-01',
    '__Kapitalwert je Quadratmeter' => NULL,
    '__Kaufpreis pro Quadratmeter in Euro' => 1300000.0,
    '__Kaufpreis pro Quadratmeter in Fremdwährung' => 1400000.0,
    '__Kaufpreis-Feld' => '1100000.0',
    '__Kaufpreis-Typ-Feld' => 'F',
    '__Koordinaten/MercatorX' => 768966.444,
    '__Koordinaten/MercatorY' => 6613559.56,
    '__Liquidität' => NULL,
    '__Liquidität in Fremdwährung' => NULL,
    '__LoI-Kaufpreis' => 1700000.0,
    '__LoI-Kaufpreis in Fremdwährung' => 1800000.0,
    '__MaklerAdresse/Adresszusatz 1' => 'd',
    '__MaklerAdresse/Adresszusatz 2' => 'e',
    '__MaklerAdresse/Ansprechpartner' => 'b',
    '__MaklerAdresse/Firmenname' => 'a',
    '__MaklerAdresse/Land' => 'Haiti',
    '__MaklerAdresse/Region' => 'g',
    '__MaklerAdresse/Stadt' => '12345 f',
    '__MaklerAdresse/Strasse' => 'c 1',
    '__MapViewer/Koordinaten/Luftbild/Center' => '50.95637254895154,6.907735055480009',
    '__MapViewer/Koordinaten/Luftbild/Zoom' => 19.0,
    '__MapViewer/Koordinaten/Makrolage/Center' => '50.96058107173877,6.918729428125019',
    '__MapViewer/Koordinaten/Makrolage/Zoom' => 11.0,
    '__MapViewer/Koordinaten/Mikrolage/Center' => '50.95636413169991,6.9078021085983465',
    '__MapViewer/Koordinaten/Mikrolage/Zoom' => 18.0,
    '__Meilensteine' => '1. urlaub 2. weltherrschaft',
    '__Miete je Quadratmeter' => 300000.0,
    '__Miete je Squarefeet in Fremdwährung' => 400000.0,
    '__Mietfläche' => 200000.0,
    '__Mietfläche Kommentar' => 'mietfl anm',
    '__NOI p.a.' => 900000.0,
    '__NOI p.a. in Fremdwährung' => 1000000.0,
    '__Netto-Anfangsrendite' => 2345.0,
    '__Nutzungstyp' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10003', 'value' => 'Logistik', 'id' => '10003',],
    '__Objektstrategie' => 'links antäuschen, rechts vorbei',
    '__Projektart' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10363', 'value' => 'Projektentwicklung', 'id' => '10363',],
    '__Revitalisierung' => 2002.0,
    '__Revitalisierung-Kommentar' => 'rev anm',
    '__Risikoklasse' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10370', 'value' => 'Opportunistic', 'id' => '10370',],
    '__Schwächen' => 'blöde nachbarn',
    '__Soll-Miete p.a.' => 700000.0,
    '__Soll-Miete p.a. in Fremdwährung' => 800000.0,
    '__Stärken' => 'tolle lage',
    '__Teilmarkt' => 'null plan',
    '__Transaktions-Währung' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10333', 'value' => 'SSP', 'id' => '10333',],
    '__TransaktionsAdresse/Adresszusatz 1' => 'Hinterhof',
    '__TransaktionsAdresse/Adresszusatz 2' => '1. Etage',
    '__TransaktionsAdresse/Land' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10047', 'value' => 'Deutschland', 'id' => '10047',],
    '__TransaktionsAdresse/Region' => 'NRW',
    '__TransaktionsAdresse/Stadt' => 'Köln',
    '__TransaktionsAdresse/Strasse' => 'Alpenerstr. 16',
    '__Transaktionsstruktur' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10373', 'value' => 'Share Deal', 'id' => '10373',],
    '__Verkaufsstrategie/Verkaufsfördernde Maßnahmen' => 'foo',
    '__Verkaufsstrategie/Verkaufsgrund' => 'bar',
    '__Verkaufswert' => 2200000.0,
    '__Verkaufswert Stichtag' => '2020-01-01',
    '__Verkaufswert in Fremdwährung' => 2300000.0,
    '__Verkehrswert' => 2400000.0,
    '__Vermietungsstand' => 6900.0,
    '__VertragspartnerAdresse/Adresszusatz 1' => 'dd',
    '__VertragspartnerAdresse/Adresszusatz 2' => 'ee',
    '__VertragspartnerAdresse/Ansprechpartner' => 'bb',
    '__VertragspartnerAdresse/Firmenname' => 'aa',
    '__VertragspartnerAdresse/Land' => 'Honduras',
    '__VertragspartnerAdresse/Region' => 'gg',
    '__VertragspartnerAdresse/Stadt' => '23456 ff',
    '__VertragspartnerAdresse/Strasse' => 'cc 2',
    '__WALB' => 1233.0,
    '__WAULT' => 1146.0,
    '__Wirtschaftsraum' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/customFieldOption/10382', 'value' => 'APAC', 'id' => '10382',],
    '__YIELD' => 1234.0,
    '__Übergang BNL/Datum' => '2020-06-11',
    '__Übergang BNL/Quartal' => '2020-04-01',
    'aggregateprogress' => ['progress' => 0, 'total' => 0,],
    'aggregatetimeestimate' => NULL,
    'aggregatetimeoriginalestimate' => NULL,
    'aggregatetimespent' => NULL,
    'archivedby' => NULL,
    'archiveddate' => NULL,
    'assignee' => NULL,
    'attachment' => [],
    'comment' => ['comments' => [0 => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/issue/10318/comment/10100', 'id' => '10100', 'author' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/user?username=admin', 'name' => 'admin', 'key' => 'admin', 'emailAddress' => 'dev-user+admin@seibert-media.net', 'avatarUrls' => ['48x48' => 'http://host.docker.internal:2990/jira/secure/useravatar?ownerId=admin&avatarId=10606', '24x24' => 'http://host.docker.internal:2990/jira/secure/useravatar?size=small&ownerId=admin&avatarId=10606', '16x16' => 'http://host.docker.internal:2990/jira/secure/useravatar?size=xsmall&ownerId=admin&avatarId=10606', '32x32' => 'http://host.docker.internal:2990/jira/secure/useravatar?size=medium&ownerId=admin&avatarId=10606',], 'displayName' => 'admin', 'active' => true, 'timeZone' => 'Europe/Berlin',], 'body' => 'b', 'updateAuthor' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/user?username=admin', 'name' => 'admin', 'key' => 'admin', 'emailAddress' => 'dev-user+admin@seibert-media.net', 'avatarUrls' => ['48x48' => 'http://host.docker.internal:2990/jira/secure/useravatar?ownerId=admin&avatarId=10606', '24x24' => 'http://host.docker.internal:2990/jira/secure/useravatar?size=small&ownerId=admin&avatarId=10606', '16x16' => 'http://host.docker.internal:2990/jira/secure/useravatar?size=xsmall&ownerId=admin&avatarId=10606', '32x32' => 'http://host.docker.internal:2990/jira/secure/useravatar?size=medium&ownerId=admin&avatarId=10606',], 'displayName' => 'admin', 'active' => true, 'timeZone' => 'Europe/Berlin',], 'created' => '2020-06-15T16:51:23.253+0200', 'updated' => '2020-06-15T16:51:23.253+0200',],], 'maxResults' => 1, 'total' => 1, 'startAt' => 0,],
    'components' => [],
    'created' => '2020-06-15T15:57:02.294+0200',
    'creator' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/user?username=migrations-runner', 'name' => 'migrations-runner', 'key' => 'JIRAUSER10100', 'emailAddress' => 'dev-user+ObjectNames.MIGRATIONS_RUNNER_USER@seibert-media.net', 'avatarUrls' => ['48x48' => 'https://www.gravatar.com/avatar/420e3e4b8bdba0aa0e4a4f48f082f38b?d=mm&s=48', '24x24' => 'https://www.gravatar.com/avatar/420e3e4b8bdba0aa0e4a4f48f082f38b?d=mm&s=24', '16x16' => 'https://www.gravatar.com/avatar/420e3e4b8bdba0aa0e4a4f48f082f38b?d=mm&s=16', '32x32' => 'https://www.gravatar.com/avatar/420e3e4b8bdba0aa0e4a4f48f082f38b?d=mm&s=32',], 'displayName' => 'Migrations Runner', 'active' => true, 'timeZone' => 'Europe/Berlin',], 'description' => NULL, 'duedate' => NULL, 'environment' => NULL, 'fixVersions' => [], 'issuelinks' => [], 'issuetype' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/issuetype/10000', 'id' => '10000', 'description' => 'Eine CRx-Transaktion für den Ankauf eines Objekts', 'iconUrl' => 'http://host.docker.internal:2990/jira/secure/viewavatar?size=xsmall&avatarId=10311&avatarType=issuetype', 'name' => 'CRx Ankauf', 'subtask' => false, 'avatarId' => 10311,], 'key' => 'CRX-319', 'labels' => [], 'lastViewed' => '2020-06-15T17:21:38.719+0200', 'priority' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/priority/1', 'iconUrl' => 'http://host.docker.internal:2990/jira/images/icons/priorities/highest.svg', 'name' => 'Highest', 'id' => '1',], 'progress' => ['progress' => 0, 'total' => 0,], 'project' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/project/10000', 'id' => '10000', 'key' => 'CRX', 'name' => 'CRx', 'projectTypeKey' => 'business', 'avatarUrls' => ['48x48' => 'http://host.docker.internal:2990/jira/secure/projectavatar?avatarId=10324', '24x24' => 'http://host.docker.internal:2990/jira/secure/projectavatar?size=small&avatarId=10324', '16x16' => 'http://host.docker.internal:2990/jira/secure/projectavatar?size=xsmall&avatarId=10324', '32x32' => 'http://host.docker.internal:2990/jira/secure/projectavatar?size=medium&avatarId=10324',],], 'reporter' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/user?username=migrations-runner', 'name' => 'migrations-runner', 'key' => 'JIRAUSER10100', 'emailAddress' => 'dev-user+ObjectNames.MIGRATIONS_RUNNER_USER@seibert-media.net', 'avatarUrls' => ['48x48' => 'https://www.gravatar.com/avatar/420e3e4b8bdba0aa0e4a4f48f082f38b?d=mm&s=48', '24x24' => 'https://www.gravatar.com/avatar/420e3e4b8bdba0aa0e4a4f48f082f38b?d=mm&s=24', '16x16' => 'https://www.gravatar.com/avatar/420e3e4b8bdba0aa0e4a4f48f082f38b?d=mm&s=16', '32x32' => 'https://www.gravatar.com/avatar/420e3e4b8bdba0aa0e4a4f48f082f38b?d=mm&s=32',], 'displayName' => 'Migrations Runner', 'active' => true, 'timeZone' => 'Europe/Berlin',],
    'resolution' => NULL,
    'resolutiondate' => NULL,
    'status' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/status/10000', 'description' => '', 'iconUrl' => 'http://host.docker.internal:2990/jiracrx.png', 'name' => 'Neue Angebote', 'id' => '10000', 'statusCategory' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/statuscategory/2', 'id' => 2, 'key' => 'new', 'colorName' => 'blue-gray', 'name' => 'Aufgaben',],],
    'subtasks' => [],
    'summary' => 'Mietfläche in Qm und Grundstücksgröße in Square Feet',
    'timeestimate' => NULL,
    'timeoriginalestimate' => NULL,
    'timespent' => NULL,
    'timetracking' => [],
    'updated' => '2020-06-16T10:11:39.605+0200',
    'versions' => [],
    'votes' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/issue/CRX-319/votes', 'votes' => 0, 'hasVoted' => false,],
    'watches' => ['self' => 'http://host.docker.internal:2990/jira/rest/api/2/issue/CRX-319/watchers', 'watchCount' => 2, 'isWatching' => true,],
    'worklog' => ['startAt' => 0, 'maxResults' => 20, 'total' => 0, 'worklogs' => [],], 'workratio' => -1,];

function int(): callable
{
    return static function ($value) {
        return (int) $value;
    };
}

function intOrNull(): callable
{
    return static function ($value) {
        return strlen((string) $value) ? (int) $value : null;
    };
}

function float(): callable
{
    return static function ($value) {
        return (float) $value;
    };
}

function dezimal(): callable
{
    return static function ($value) {
        return $value / 100;
    };
}

function prozent(): callable
{
    return static function ($value) {
        return $value / 100;
    };
}

function quartal(): callable
{
    return static function ($value) {
        $date = new DateTimeImmutable($value);
        $quarter = ceil((int) $date->format('m') / 3);
        return sprintf('Q%d/%d', $quarter, $date->format('Y'));
    };
}

function jahr(): callable
{
    return static function ($value) {
        return (int) $value;
    };
}

function kaufpreis(): callable
{
    return static function ($value) {
        $map = [
            'F' => 'Finaler Kaufpreis',
            'L' => 'LOI Kaufpreis',
            'A' => 'Initialer Angebotskaufpreis',
        ];
        if (!array_key_exists($value, $map)) {
            throw new RuntimeException(sprintf('Unknown Kaufpreis: %s', $value));
        }

        return $map[$value];
    };
}

const TSUBO_TO_QM_FACTOR = 3.30579;
const SQFT_TO_QM_FACTOR = 0.09290304;

function flaeche(): callable
{
    return static function ($value, $unit) {

        if ($unit === 'Tsubo') {
            $qm = $value * \TSUBO_TO_QM_FACTOR;
        } elseif ($unit === 'ft²') {
            $qm = $value * \SQFT_TO_QM_FACTOR;
        } elseif ($unit === 'm²') {
            $qm = (float) $value;
        } else {
            throw new RuntimeException(sprintf('Unknown Flächeneinheit: %s', $unit));
        }

        return [
            'qm' => $qm,
            'tsubo' => $qm / TSUBO_TO_QM_FACTOR,
            'sqft' => $qm / SQFT_TO_QM_FACTOR,
        ];

    };
}

function allokation(): callable
{
    return static function ($value) {
        $fonds = [];
        if (is_array($value) && array_key_exists('fonds', $value)) {
            foreach ($value['fonds'] as $key => $fond) {
                if (!array_key_exists('vorgeschlagen', $fond) || !$fond['vorgeschlagen']) {
                    continue;
                }

                $fonds[] = [
                    'fond' => $key,
                    'initialesVotum' => $fond['initialesVotum'] ?? null,
                    'initialesVotumBegruendung' => $fond['initialesVotumBegruendung'] ?? null,
                    'zweitesVotum' => $fond['zweitesVotum'] ?? null,
                    'zweitesVotumBegruendung' => $fond['zweitesVotumBegruendung'] ?? null,
                ];
            }
        }

        return [
            'fonds' => $fonds,
            'finaleEntscheidung' => $value['finaleEntscheidungId'] ?? null,
        ];
    };
}

function coordsX(): callable
{
    return static function ($value) {
        $valueParts = explode(',', $value);
        if (!array_key_exists(0, $valueParts)) {
            return null;
        }

        return (float) $valueParts[0];
    };
}

function coordsY(): callable
{
    return static function ($value) {
        $valueParts = explode(',', $value);
        if (!array_key_exists(1, $valueParts)) {
            return null;
        }

        return (float) $valueParts[1];
    };
}

function kommentare(): callable
{
    return static function ($value) {
        $comments = [];
        foreach ($value as $comment) {
            $comments[] = [
                'erstellt' => $comment['created'],
                'erstelltVon' => $comment['author']['displayName'],
                'aktualisiert' => $comment['updated'],
                'aktualisiertVon' => $comment['updateAuthor']['displayName'],
                'text' => $comment['body'],
            ];
        }

        return $comments;
    };
}

$fn = applySpec([
    'administrativeNotizen' => prop('__Administrative Notizen'),
    'allokation' => pipe(prop('__Allokation'), allokation()),
    'angebotsdatum' => prop('__Angebotsdatum'),
    'anzahlHotelzimmer' => pipe(prop('__Anzahl Hotelzimmer'), int()),
    'anzahlStellplaetze' => [
        'innen' => pipe(prop('__Anzahl Stellplätze innen'), int()),
        'aussen' => pipe(prop('__Anzahl Stellplätze außen'), int()),
        'gesamt' => pipe(prop('__Anzahl Stellplätze gesamt'), int()),
    ],
    'anzahlMieter' => pipe(prop('__Anzahl der Mieter'), int()),
    'baujahr' => [
        'jahr' => pipe(prop('__Baujahr'), jahr(), int()),
        'kommentar' => prop('__Baujahr-Kommentar'),
    ],
    'bedrohungen' => prop('__Bedrohungen'),
    'dealClosingChance' => pipe(prop('__Chance auf Deal-Closing in Prozent'), dezimal(), prozent()),
    'chancen' => prop('__Chancen'),
    'datenraumunterlagen' => prop('__Datenraumunterlagen'),
    'eigentumsform' => pipe(prop('__Eigentumsform'), prop('value')),
    'empfehlung' => pipe(prop('__Empfehlung'), prop('value')),
    'finalerKaufpreis' => [
        'euro' => pipe(prop('__Finaler Kaufpreis'), int()),
        'fremdwaehrung' => pipe(prop('__Finaler Kaufpreis in Fremdwährung'), int()),
    ],
    'flächeneinheit' => pipe(prop('__Flächeneinheit'), prop('value')),
    'fonds' => prop('__Fonds'),
    'fremdkapitalquote' => pipe(prop('__Fremdkapitalquote in Prozent'), dezimal(), prozent()),
    'funding' => pipe(prop('__Funding'), prop('value')),
    'gebuehr' => [
        'euro' => pipe(prop('__Gebühr für die CR in Eurocent'), dezimal()),
        'prozent' => pipe(prop('__Gebühr für die CR in Prozent'), dezimal(), prozent()),
    ],
    'grundstuecksgroesse' => converge(flaeche(), [
        pipe(prop('__Grundstücksgröße'), dezimal(), float()),
        pipe(prop('__Flächeneinheit'), prop('value')),
    ]),
    'hauptmieter' => prop('__Hauptmieter'),
    'investmentmanager' => pipe(prop('__Investmentmanager'), prop('displayName')),
    'initialerAngebotskaufpreis' => [
        'euro' => pipe(prop('__Initialer Angebotskaufpreis'), int()),
        'fremdwaehrung' => pipe(prop('__Initialer Angebotskaufpreis in Fremdwährung'), int()),
    ],
    'investitionskostenQm' => [
        'euro' => pipe(prop('__Investitionskosten pro Quadratmeter in Euro'), int()),
        'fremdwaehrung' => pipe(prop('__Investitionskosten pro Quadratmeter in Fremdwährung'), int()),
    ],
    'istMiete' => [
        'euro' => pipe(prop('__Ist-Miete p.a.'), int()),
        'fremdwaehrung' => pipe(prop('__Ist-Miete p.a. in Fremdwährung'), int()),
    ],
    'kapitalwertQm' => ifElse(
        compose(complement(isEmpty()), prop('__Kapitalwert je Quadratmeter')),
        applySpec([
            'euro' => pipe(prop('__Kapitalwert je Quadratmeter'), int()),
        ]),
        always(null),
    ),
    'kaufpreis' => [
        'euro' => pipe(prop('__Kaufpreis-Feld'), int()),
        'typ' => pipe(prop('__Kaufpreis-Typ-Feld'), kaufpreis()),
    ],
    'kaufpreisQm' => [
        'euro' => pipe(prop('__Kaufpreis pro Quadratmeter in Euro'), int()),
        'fremdwaehrung' => pipe(prop('__Kaufpreis pro Quadratmeter in Fremdwährung'), int()),
    ],
    'koordinaten' => [
        'x' => prop('__Koordinaten/MercatorX'),
        'y' => prop('__Koordinaten/MercatorY'),
    ],
    'kvAbschluss' => [
        'datum' => prop('__KV-Abschluss/Datum'),
        'quartal' => pipe(prop('__KV-Abschluss/Quartal'), quartal()),
    ],
    'liquiditaet' => ifElse(
        pipe(
            applySpec([
                prop('__Liquidität'),
                prop('__Liquidität in Fremdwährung'),
            ]),
            filter(complement(isEmpty()))
        ),
        applySpec([
            'euro' => pipe(prop('__Liquidität'), int()),
            'fremdwaehrung' => pipe(prop('__Liquidität in Fremdwährung'), int()),
        ]),
        always(null),
    ),
    'loiKaufpreis' => [
        'euro' => pipe(prop('__LoI-Kaufpreis'), int()),
        'fremdwaehrung' => pipe(prop('__LoI-Kaufpreis in Fremdwährung'), int()),
    ],
    'maklerAdresse' => [
        'adresszusatz1' => prop('__MaklerAdresse/Adresszusatz 1'),
        'adresszusatz2' => prop('__MaklerAdresse/Adresszusatz 2'),
        'ansprechpartner' => prop('__MaklerAdresse/Ansprechpartner'),
        'firmenname' => prop('__MaklerAdresse/Firmenname'),
        'land' => prop('__MaklerAdresse/Land'),
        'region' => prop('__MaklerAdresse/Region'),
        'stadt' => prop('__MaklerAdresse/Stadt'),
        'strasse' => prop('__MaklerAdresse/Strasse'),
    ],
    'maps' => [
        'luftbild' => [
            'x' => pipe(prop('__MapViewer/Koordinaten/Luftbild/Center'), coordsX()),
            'y' => pipe(prop('__MapViewer/Koordinaten/Luftbild/Center'), coordsY()),
            'zoom' => pipe(prop('__MapViewer/Koordinaten/Luftbild/Zoom'), int()),
        ],
        'makrolage' => [
            'x' => pipe(prop('__MapViewer/Koordinaten/Makrolage/Center'), coordsX()),
            'y' => pipe(prop('__MapViewer/Koordinaten/Makrolage/Center'), coordsY()),
            'zoom' => pipe(prop('__MapViewer/Koordinaten/Makrolage/Zoom'), int()),
        ],
        'mikrolage' => [
            'x' => pipe(prop('__MapViewer/Koordinaten/Mikrolage/Center'), coordsX()),
            'y' => pipe(prop('__MapViewer/Koordinaten/Mikrolage/Center'), coordsY()),
            'zoom' => pipe(prop('__MapViewer/Koordinaten/Mikrolage/Zoom'), int()),
        ],
    ],
    'meilensteine' => prop('__Meilensteine'),
    'miete' => [
        'euro' => pipe(prop('__Miete je Quadratmeter'), int()),
        'fremdwaehrung' => pipe(prop('__Miete je Squarefeet in Fremdwährung'), int()),
    ],
    'mietflaeche' => converge(merge(), [
        converge(flaeche(), [
            pipe(prop('__Mietfläche'), dezimal(), float()),
            pipe(prop('__Flächeneinheit'), prop('value')),
        ]),
        applySpec([
            'kommentar' => prop('__Mietfläche Kommentar'),
        ]),
    ]),
    'noi' => [
        'euro' => pipe(prop('__NOI p.a.'), int()),
        'fremdwaehrung' => pipe(prop('__NOI p.a. in Fremdwährung'), int()),
    ],
    'nettoAnfangsrendite' => pipe(prop('__Netto-Anfangsrendite'), dezimal(), prozent()),
    'nutzungstyp' => pipe(prop('__Nutzungstyp'), prop('value')),
    'objektStrategie' => prop('__Objektstrategie'),
    'projektart' => pipe(prop('__Projektart'), prop('value')),
    'revitalisierung' => [
        'jahr' => pipe(prop('__Revitalisierung'), int()),
        'kommentar' => prop('__Revitalisierung-Kommentar'),
    ],
    'risikoklasse' => pipe(prop('__Risikoklasse'), prop('value')),
    'schwaechen' => prop('__Schwächen'),
    'sollMiete' => [
        'euro' => pipe(prop('__Soll-Miete p.a.'), int()),
        'fremdwaehrung' => pipe(prop('__Soll-Miete p.a. in Fremdwährung'), int()),
    ],
    'staerken' => prop('__Stärken'),
    'teilmarkt' => prop('__Teilmarkt'),
    'fremdwaehrung' => pipe(prop('__Transaktions-Währung'), prop('value')),
    'transaktionsAdresse' => [
        'adresszusatz1' => prop('__TransaktionsAdresse/Adresszusatz 1'),
        'adresszusatz2' => prop('__TransaktionsAdresse/Adresszusatz 2'),
        'land' => prop('__TransaktionsAdresse/Land'),
        'region' => prop('__TransaktionsAdresse/Region'),
        'stadt' => prop('__TransaktionsAdresse/Stadt'),
        'strasse' => prop('__TransaktionsAdresse/Strasse'),
    ],
    'transaktionsStruktur' => pipe(prop('__Transaktionsstruktur'), prop('value')),
    'verkaufsstrategie' => [
        'verkaufsfoerderndeMassnahmen' => prop('__Verkaufsstrategie/Verkaufsfördernde Maßnahmen'),
        'verkaufsgrund' => prop('__Verkaufsstrategie/Verkaufsgrund'),
    ],
    'verkaufswert' => ifElse(
        pipe(
            applySpec([
                prop('__Verkaufswert'),
                prop('__Verkaufswert in Fremdwährung'),
                prop('__Verkaufswert Stichtag'),
            ]),
            filter(complement(isEmpty()))
        ),
        always(null),
        applySpec([
            'euro' => pipe(prop('__Verkaufswert'), intOrNull()),
            'fremdwaehrung' => pipe(prop('__Verkaufswert in Fremdwährung'), intOrNull()),
            'stichtag' => pipe(prop('__Verkaufswert Stichtag')),
        ]),
    ),
    'verkehrswert' => ifElse(
        compose(complement(isEmpty()), prop('__Verkehrswert')),
        applySpec([
            'euro' => pipe(prop('__Verkehrswert'), int()),
        ]),
        always(null),
    ),
    'vermietungsstand' => pipe(prop('__Vermietungsstand'), dezimal(), prozent()),
    'vertragspartnerAdresse' => [
        'adresszusatz1' => prop('__VertragspartnerAdresse/Adresszusatz 1'),
        'adresszusatz2' => prop('__VertragspartnerAdresse/Adresszusatz 2'),
        'ansprechpartner' => prop('__VertragspartnerAdresse/Ansprechpartner'),
        'firmenname' => prop('__VertragspartnerAdresse/Firmenname'),
        'land' => prop('__VertragspartnerAdresse/Land'),
        'region' => prop('__VertragspartnerAdresse/Region'),
        'stadt' => prop('__VertragspartnerAdresse/Stadt'),
        'strasse' => prop('__VertragspartnerAdresse/Strasse'),
    ],
    'walb' => pipe(prop('__WALB'), dezimal(), float()),
    'wault' => pipe(prop('__WAULT'), dezimal(), float()),
    'wirtschaftsraum' => pipe(prop('__Wirtschaftsraum'), prop('value')),
    'yield' => pipe(prop('__YIELD'), dezimal(), prozent()),
    'uebergangBnl' => [
        'datum' => prop('__Übergang BNL/Datum'),
        'quartal' => pipe(prop('__Übergang BNL/Quartal'), quartal()),
    ],
    'name' => prop('summary'),
    'meta' => [
        'key' => prop('key'),
        'status' => pipe(prop('status'), prop('name')),
        'type' => pipe(prop('issuetype'), prop('name')),
        'created' => prop('created'),
        'updated' => prop('updated'),
        'creator' => pipe(prop('creator'), prop('name')),
        'reporter' => pipe(prop('reporter'), prop('name')),
    ],
    'kommentare' => pipe(prop('comment'), prop('comments'), kommentare()),
]);

echo 'Input:' . PHP_EOL;
dump($data);

echo 'Output:' . PHP_EOL;
dump($fn($data));
