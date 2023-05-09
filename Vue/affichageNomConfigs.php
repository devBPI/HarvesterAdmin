<?php if(!empty($dataConf)){?>
<table class="table-backoffice">
	<th>ID Configuration</th>
	<th>Nom abrégé</th>
	<?php
    /* Lignes */
    foreach ($dataConf as $dataConfig) {
        ?><tr><?php

        /* Colonne 1 (ID Configuration) */
        ?><td><?php
        echo $dataConfig['id'];

        /* COLONNE 2 (Nom abrégé) */
        ?></td>
		<td><?php
        echo $dataConfig['name'];
        ?>
		 </td>
	</tr> <?php
    }

    ?>
	</table>
<?php } ?>
