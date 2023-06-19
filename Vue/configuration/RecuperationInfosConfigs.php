<table class="tab_fiche_individuelle">
	<thead>
	<tr>
		<th class="left_column"><?= $data['id'] ?></th>
		<th class="right_column"><?= $data['code'] ?>
			<a href="../Controlleur/ModifConfiguration.php?param=<?php echo $_GET['param'] ?>">
				<img src="../ressources/edit.png" alt="Modifier la configuration" style="float:right" width="30px" height="30px"/>
			</a>
		</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td class="left_column">Nom abrégé</td>
		<td class="right_column"><?= $data['name'] ?></td>
	</tr>
	<tr>
		<td class="left_column">Base de recherche</td>
		<td class="right_column"><?= $data['public_name'] ?></td>
	<tr>
		<td class="left_column">URL Publique</td>
		<td class="right_column"><?= $data['public_url'] ?></td>
	</tr>
	<tr>
		<td class="left_column">Connecteur</td>
		<td class="right_column"><?php echo ($data['grabber_name']!="") ? $data['grabber_name'] : ""; ?></td>
	</tr>
	<tr>
		<th colspan="2">Mapping</th>
	</tr>
	<tr>
		<td class="left_column">ID Mapping</td>
		<td class="right_column"><?php echo ($data['mapping_id']!="") ? $data['mapping_id'] : ""; ?></td>
	</tr>
	<tr>
		<td class="left_column">Nom Mapping</td>
		<td class="right_column"><?php echo ($data['mapping_name']!="") ? "<a href='../Controlleur/Mapping.php?param=".$data['mapping_id']."'>".$data['mapping_name']."</a>" : "Null"; ?></td>
	</tr>
	<tr>
		<th colspan="2">Filtres</th>
	</tr>
	<?php if($data['filters']) {
	foreach ($data['filters'] as $filter) { ?>
	<tr>
		<td class="left_column">Filtre n°<?= $filter['id'] ?></td>
		<td class="right_column"><a href="../Controlleur/FiltreTree.php?id=<?= $filter['id'] ?>"><?= $filter['name'] ?></a> portant sur "<?= $filter['entity'] ?>"</td>
	</tr>
	<?php }} ?>
	<tr>
		<th colspan="2">Traductions</th>
	</tr>
	<?php if($data['trad']) {
	foreach ($data['trad'] as $trad) { ?>
	<tr>
		<td class="left_column">Traduction n°<?= $trad['id'] ?></td>
		<td class="right_column"><a href="../Controlleur/TraductionSet.php?f=true&modify=<?= $trad['name'] ?>"><?= $trad['name'] ?></a></td>
	</tr>
	<?php }} ?>
	<tr>
		<th colspan="2">URLs</th>
	</tr>
	<tr>
		<td class="left_column">URL</td>
		<td class="right_column"><?php echo ($data['url']!="") ? $data['url'] : ""; ?></td>
	</tr>
	<tr>
		<td class="left_column">URL set</td>
		<td class="right_column"><?php echo ($data['url_set']!="") ? $data['url_set'] : ""; ?></td>
	</tr>
	<tr>
		<th colspan="2">Autres</th>
	</tr>
	<tr>
		<td class="left_column">Séparateur CSV</td>
		<td class="right_column"><?php echo ($data['csv_separator']!="") ? $data['csv_separator'] : ""; ?></td>
	</tr>
	<tr>
		<td class="left_column">Différentiel</td>
		<td class="right_column"><?php echo ($data['differential']!="f") ? "Vrai" : "Faux"; ?></td>
	</tr>
	<tr>
		<td class="left_column">Nombre maximum de tentatives</td>
		<td class="right_column"><?php echo ($data['max_attempts_number']!="") ? $data['max_attempts_number'] : ""; ?></td>
	</tr>
	<tr>
		<td class="left_column">Timeout</td>
		<td class="right_column"><?php echo ($data['timeout_sec']!="") ? $data['timeout_sec'] : ""; ?></td>
	</tr>
	<tr>
		<td class="left_column">Préfixe business ID</td>
		<td class="right_column"><?php echo ($data['business_base_prefix']!="") ? $data['business_base_prefix'] : ""; ?></td>
	</tr>
	<tr>
		<td class="left_column">Subordonnée à la configuration</td>
		<td class="right_column"><?php echo ($data['additional_configuration_of']!="") ? $data['additional_configuration_of'] : ""; ?></td>
	</tr>
	<tr>
		<td class="left_column">Type de document</td>
		<td class="right_column"><?= $data['default_document_type'] ?></td>
	</tr>
	<tr>
		<td class="left_column">Parcours</td>
		<td class="right_column"><?php
			foreach ($data['parcours'] as $val_mapping) {
				if ($val_mapping['parcours'] != null)
					echo $val_mapping['parcours'] . "<br/>";
			}
			?></td>
	</tr>
	<tr>
		<td class="left_column">Format Natif des données exposées</td>
		<td class="right_column">Indisponible</td>
	</tr>
	<tr>
		<td class="left_column">Accès</td>
		<td class="right_column"><?php
			foreach($data['profile'] as $acces)
			{
				echo $acces . "<br/>";
			}
			?></td>
	</tr>
	<tr>
		<th colspan="2">Commentaires</th>
	</tr>
	<tr>
		<td colspan="2"><?= $data['note'] ?></td>
	</tr>
	</tbody>
</table>