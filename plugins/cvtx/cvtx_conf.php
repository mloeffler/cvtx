<?php

$aeformat = get_option('cvtx_aeantrag_format');
$aepdf    = get_option('cvtx_aeantrag_pdf');
$pdflatex = get_option('cvtx_pdflatex_cmd');
$texfile  = get_option('cvtx_drop_texfile');
$logfile  = get_option('cvtx_drop_logfile');

?>

<h2>cvtx Konfiguration</h2>

<?php
if (isset($ms) && count($ms) > 0) {
    echo('<ul>');
    foreach ($ms as $msg) {
        if ($msg == 'no_cvtx_pdflatex_cmd') {
            echo('<li>Kein Pfad angegeben. LaTeX kann so nicht funktionieren, Mensch.</li>');
        }
    }
    echo('</ul>');    
}
?>

<div class="narrow">
 <form action="" method="post" id="akismet-conf" style="margin: auto; width: 400px;">
 
  <h3>Einstellungen Änderungsanträge</h3>
  <p>
   <label for="cvtx_aeantrag_format">Kurzbezeichnung für Änderungsanträge:</label> (%antrag%, %zeile%)<br />
   <input id="cvtx_aeantrag_format" name="cvtx_aeantrag_format" type="text" value="<?php echo($aeformat ? $aeformat : '%antrag%-%zeile%'); ?>" />
   <br />
   <input id="cvtx_aeantrag_pdf" name="cvtx_aeantrag_pdf" type="checkbox" <?php echo($aepdf ? 'checked="checked"' : ''); ?>" />
   <label for="cvtx_aeantrag_pdf">PDF-Versionen für Änderungsanträge erzeugen</label>
  </p>

  <h3>LaTeX-Einstellungen</h3>
  <p>
   <label for="cvtx_pdflatex_cmd">Pfad:</label><br />
   <input id="cvtx_pdflatex_cmd" name="cvtx_pdflatex_cmd" type="text" value="<?php echo($pdflatex); ?>" />
  </p>
  <p>
   <label>Sollen die erzeugten tex-Files gelöscht werden?</label><br />
   <input id="cvtx_drop_texfile_yes" name="cvtx_drop_texfile" type="radio" value="1" <?php echo($texfile == 1 ? 'checked="checked"' : ''); ?>" />
   <label for="cvtx_drop_texfile_yes">immer</label>
   <input id="cvtx_drop_texfile_if" name="cvtx_drop_texfile" type="radio" value="2" <?php echo($texfile != 1 && $texfile != 3 ? 'checked="checked"' : ''); ?>" />
   <label for="cvtx_drop_texfile_if">nur wenn fehlerfrei</label>
   <input id="cvtx_drop_texfile_no" name="cvtx_drop_texfile" type="radio" value="3" <?php echo($texfile == 3 ? 'checked="checked"' : ''); ?>" />
   <label for="cvtx_drop_texfile_no">nie</label>
   <br />
   <label>Sollen die erzeugten log-Files gelöscht werden?</label><br />
   <input id="cvtx_drop_logfile_yes" name="cvtx_drop_logfile" type="radio" value="1" <?php echo($logfile == 1 ? 'checked="checked"' : ''); ?>" />
   <label for="cvtx_drop_logfile_yes">immer</label>
   <input id="cvtx_drop_logfile_if" name="cvtx_drop_logfile" type="radio" value="2" <?php echo($logfile != 1 && $logfile != 3 ? 'checked="checked"' : ''); ?>" />
   <label for="cvtx_drop_logfile_if">nur wenn fehlerfrei</label>
   <input id="cvtx_drop_logfile_no" name="cvtx_drop_logfile" type="radio" value="3" <?php echo($logfile == 3 ? 'checked="checked"' : ''); ?>" />
   <label for="cvtx_drop_logfile_no">nie</label>
  </p>

  <p class="submit">
   <input type="submit" name="submit" value="Einstellungen speichern" />
  </p>
 </form>
</div>