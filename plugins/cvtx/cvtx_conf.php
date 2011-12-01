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
   <input id="cvtx_aeantrag_format" name="cvtx_aeantrag_format" type="text" value="<?php echo(get_option('cvtx_aeantrag_format')); ?>" />
   <br />
   <input id="cvtx_aeantrag_pdf" name="cvtx_aeantrag_pdf" type="checkbox" <?php echo(get_option('cvtx_aeantrag_pdf') ? 'checked="checked"' : ''); ?>" />
   <label for="cvtx_aeantrag_pdf">PDF-Versionen für Änderungsanträge erzeugen</label>
  </p>

  <h3>LaTeX-Einstellungen</h3>
  <p>
   <label for="cvtx_pdflatex_cmd">Pfad:</label><br />
   <input id="cvtx_pdflatex_cmd" name="cvtx_pdflatex_cmd" type="text" value="<?php echo(get_option('cvtx_pdflatex_cmd')); ?>" />
  </p>

  <p class="submit">
   <input type="submit" name="submit" value="Einstellungen speichern" />
  </p>
 </form>
</div>