	<p style="border-bottom: 1px solid;">
    ID de la configuration : <?= $data['id'] ?>
	<br>
	Code de la configuration : <?= $data['code'] ?>
	<br>
	Nom abrégé : <?= $data['name'] ?>
	<br>
	Nom Public : <?= $data['public_name'] ?>
	<br>
	URL Publique : <?= $data['public_url'] ?>
	<br>
	Connecteur :
	<?php
	echo ($data['grabber_name']!="") ? $data['grabber_name'] : "Null";
    ?>
	<br>
	<br>
</p>
<p style="border-bottom: 1px solid;">
	ID Mapping :
	<?php
	echo ($data['mapping_id']!="") ? $data['mapping_id'] : "Null";
    ?>
	<br>
    Nom Mapping :
	<?php
	echo ($data['mapping_name']!="") ? "<a href='../Controlleur/Mapping.php?param=".$data['mapping_id']."'>".$data['mapping_name']."</a>" : "Null";
    ?>
	<br>
	<br>
</p>
<p style="border-bottom: 1px solid;">
	ID Filtre :
	<?php
	echo ($data['filter_id']!="") ? $data['filter_id'] : "Null";
    ?>
	<br>
    Nom Filtre :
	<?php
	echo ($data['exclusion_name']!="") ? "<a href='../Controlleur/Exclusion.php?param=".$data['filter_id']."'>".$data['exclusion_name']."</a>" : "Null";
    ?>
	<br>
	<br>
</p>
<p style="border-bottom: 1px solid;">
	<?php
    echo "Traduction : ";
	foreach($data['trad'] as $t)
	{
		echo "<br><a href='../Controlleur/TraductionSet.php?f=true&modify=".$t['name']."'>".$t['name']."</a>";
	}
    ?>
	<br>
	<br>
</p>
<p style="border-bottom: 1px solid;">
	Url :
	<?php
	echo ($data['url']!="") ? $data['url'] : "Null";
    ?>
	<br>
    Url set :
	<?php
	echo ($data['url_set']!="") ? $data['url_set'] : "Null";
    ?>
	<br>
	<br>
</p>
<p>
    Separateur CSV :
	<?php
	echo ($data['csv_separator']!="") ? $data['csv_separator'] : "Null";
    ?>
	<br>
	Différentiel :
	<?php
	echo ($data['differential']!="f") ? "Vrai" : "Faux";
    ?>
	<br>
	Nombre de tentatives :
	<?php
	echo ($data['max_attempts_number']!="") ? $data['max_attempts_number'] : "Null";
    ?>
	<br>
	Timeout :
	<?php
	echo ($data['timeout_sec']!="") ? $data['timeout_sec'] : "Null";
    ?>
	<br>
	Préfixe business ID :
	<?php
	echo ($data['business_base_prefix']!="") ? $data['business_base_prefix'] : "Null";
    ?>
	<br>
    Subordonnée à la configuration :
	<?php
    echo ($data['additional_configuration_of']!="") ? $data['additional_configuration_of'] : "Null";
	?>
	<br>
	Type de document : <?= $data['default_document_type'] ?>
	<br>
    Parcours :
	<?php
    foreach ($data['parcours'] as $val_mapping) {
        echo "<br>-".$val_mapping['parcours'];
    }
    ?>
	<br>
	Format Natif des données exposées : Indisponible
	<br>
	Accès :
	<?php
	foreach($data['profile'] as $acces)
	{
		echo "<br>-".$acces['description'];
	}
    ?>
	<br>
	<br>
	Commentaire : <?= $data['note'] ?>
	<br>
	<br>
</p>