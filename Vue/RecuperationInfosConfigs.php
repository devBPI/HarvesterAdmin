	<p style="border-bottom: 1px solid;"><?php
    echo "ID de la configuration : " . $data['id']?>
	<br>
	<?php
    echo "Code de la configuration : " . $data['code'];
    ?>
	<br>
	<?php
    echo "Nom abrégé : " . $data['name'];
    ?>
	<br>
	<?php
    echo "Nom Public : " . $data['public_name'];
    ?>
	<br>
	<?php
    echo "URL Publique : " . $data['public_url'];
    ?>
	<br>
	<?php
    echo "Connecteur : ";
	echo ($data['grabber_name']!="") ? $data['grabber_name'] : "Null";

    ?>
	<br>
	<br>
</p>
<p style="border-bottom: 1px solid;">
	<?php
    echo "ID Mapping : ";
	echo ($data['mapping_id']!="") ? $data['mapping_id'] : "Null";
    ?>
	<br>
	<?php
    echo "Nom Mapping : ";
	echo ($data['mapping_name']!="") ? "<a href='../Controlleur/Mapping.php?param=".$data['mapping_id']."'>".$data['mapping_name']."</a>" : "Null";
    ?>
	<br>
	<br>
</p>
<p style="border-bottom: 1px solid;">
	<?php
    echo "ID Filtre : ";
	echo ($data['filter_id']!="") ? $data['filter_id'] : "Null";
    ?>
	<br>
	<?php
    echo "Nom Filtre : ";
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
	<?php
    echo "Url : ";
    echo ($data['url']!="") ? $data['url'] : "Null";
    ?>
	<br>
	<?php
    echo "Url set : ";
	echo ($data['url_set']!="") ? $data['url_set'] : "Null";
    ?>
	<br>
	<br>
</p>
<p>
	<?php
    echo "Separateur CSV : ";
	echo ($data['csv_separator']!="") ? $data['csv_separator'] : "Null";
    ?>
	<br>
	<?php
    echo "Differentiel : ";
	echo ($data['differential']!="f") ? "Vrai" : "Faux";
    ?>
	<br>
	<?php
    echo "Nombre de tentatives : ";
	echo ($data['max_attempts_number']!="") ? $data['max_attempts_number'] : "Null";
    ?>
	<br>
	<?php
    echo "Timeout : ";
	echo ($data['timeout_sec']!="") ? $data['timeout_sec'] : "Null";
    ?>
	<br>
	<?php
    echo "Préfixe business ID : ";
	echo ($data['business_base_prefix']!="") ? $data['business_base_prefix'] : "Null";
    ?>
	<br>
	<?php
    echo "Subordonnée à la configuration : ";
    echo ($data['additional_configuration_of']!="") ? $data['additional_configuration_of'] : "Null";
   

    ?>
	<br>
	<?php
    echo "Type de document : " . $data['default_document_type'];
    ?>
	<br>
	<?php
    echo "Parcours : ";
    foreach ($data['parcours'] as $val_mapping) {
        echo "<br>-".$val_mapping['parcours'];
    }
    ?>
	<br>

	<?php
    echo "Format Natif des données exposées : Indisponible";
    ?>
	<br>
	<?php
	echo "Accès :";
	foreach($data['profile'] as $acces)
	{
		echo "<br>-".$acces['description'];
	}
    ?>
	<br>
	<br>
	<?php
    echo "Commentaire : " . $data['note'];
    ?>
	<br>
	<br>
</p>