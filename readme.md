# adRom WebMailReport

Type | Value
------------ | -------------
copyright | adRom Media Marketing GmbH 2015
author | Benjamin Gök <b.goek@adrom.net>
version | 1.4.2

## German
 
1. Ordner "adrom-mail-report" per FTP hochladen
2. ".../adrom-mail-report/config.php" für die Datenbank-Verbindung entsprechend editieren.
3. ".../adrom-mail-report/install.php" aufrufen (es wird ein neuer Datenbank-Table erstellt falls dieser nicht schon existiert)
	* Fehler: "Error creating table"
	* Erfolg: "Table successfully created"
4. optional kann die ".../adrom-mail-report/install.php" gelöscht werden
5. bei einem bounce/feedbackloop/sendlog wird die "receivejson.php" aufgerufen, welche den neu erstellten Table befüllt.
6. für eine Übersicht der Bounces bitte "index.php" aufrufen

### *zusätzliche Infos*
##### - $benchmark
wird die Variable **$benchmark** innerhalb von *receivejson.php* auf **true** gesetzt

```php
$benchmark = true;
```
so werden **Testdaten** generiert und in die Datenbank gespeichert, zusätzlich wird eine **timer.log**-Datei erstellt welches Folgendes beinhaltet: 
* Anzahl der gespeicherten Datensätze
* Dauer die für das Speichern benötigt wurde

`Achtung: solange $benchmark den Wert "true" beinhaltet, werden alle empfangene Datensätze mit den Testdaten überschrieben`

##### - $logging
wird die Variable **$logging** innerhalb von *receivejson.php* auf **true** gesetzt

```php
$logging = true;
```
so werden alle Datensätze die erhalten worden sind, in eine **logger.log**-Datei gespeichert.
