<h2>cvtx Konfiguration</h2>

<?php
if (isset($ms) && count($ms) > 0) {
    echo('<ul>');
    foreach ($ms as $msg) {
        if ($msg == 'no_cvtx_config_latex') {
            echo('<li>Kein Pfad angegeben. LaTeX kann so nicht funktionieren, Mensch.</li>');
        }
    }
    echo('</ul>');    
}
?>

<div class="narrow">
 <form action="" method="post" id="akismet-conf" style="margin: auto; width: 400px;">
 
  <h3>Formatierungseinstellungen</h3>
  <p>
   <label for="cvtx_config_format_aeantrag">Kurzbezeichnung für Änderungsanträge:</label> (%antrag%, %zeile%)<br />
   <input id="cvtx_config_format_aeantrag" name="cvtx_config_format_aeantrag" type="text" value="<?php echo(get_option('cvtx_config_format_aeantrag')); ?>" />
  </p>

  <h3>LaTeX-Einstellungen</h3>
  <p>
   <label for="cvtx_config_latex">Pfad:</label><br />
   <input id="cvtx_config_latex" name="cvtx_config_latex" type="text" value="<?php echo(get_option('cvtx_config_latex')); ?>" />
  </p>

  <p class="submit">
   <input type="submit" name="submit" value="Einstellungen speichern" />
  </p>
 </form>
</div>