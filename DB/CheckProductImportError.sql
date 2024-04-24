DELIMITER //

CREATE PROCEDURE CheckProductImportError(IN P_uuid CHAR(36))
BEGIN
select 
tp.stone_id, Case When p1.stone_id Is NOT Null Then 'This Stone ID is already available' else '' end as stone_id_error, 
tp.cert_no, Case When p1.cert_no Is NOT Null Then 'This Certificate No is already available' else '' end as cert_no_error, 
tp.cert_type, Case When tp.image Is Null Then 'Certificate Type cannot be null' else '' end as cert_type_error, 
tp.cert_url, Case When tp.cert_url Is Null Then 'Certificate URL cannot be null' else '' end as cert_url_error,
tp.image, Case When tp.image Is Null Then 'Image cannot be null' else '' end as image_error,
tp.video, Case When tp.video Is Null Then 'Video cannot be null' else '' end as video_error,
tp.diamond_type, Case When tp.diamond_type Is Null Then 'Diamond Type cannot be null' else '' end as diamond_type_error,
tp.size_id, Case When s.name Is Null Then 'Size does not exist' else '' end as size_id_error, 
tp.carat, Case When tp.video Is Null Then 'Video cannot be null' else '' end as video_error,
tp.shape_id, Case When sh.name Is Null Then 'Shape does not exist' else '' end as shape_id_error, 
tp.color_id, Case When c.name Is Null Then 'Color does not exist' else '' end as color_id_error, 
tp.colors_id, Case When fc.name Is Null Then 'Fancy Color does not exist' else '' end as colors_id_error,
tp.overtone_id, Case When fo.name Is Null Then 'Fancy Color Overtone does not exist' else '' end as overtone_id_error,
tp.intensity_id, Case When fi.name Is Null Then 'Fancy Color Intensity does not exist' else '' end as intensity_id_error, 
tp.clarity_id, Case When cl.name Is Null Then 'Clarity does not exist' else '' end as clarity_id_error, 
tp.cut_id, Case When cu.name Is Null Then 'Cut does not exist' else '' end as cut_id_error, 
tp.polish_id, Case When po.name Is Null Then 'Polish does not exist' else '' end as polish_id_error, 
tp.symmetry_id, Case When sy.name Is Null Then 'Symmetry does not exist' else '' end as symmetry_id_error, 
tp.fluorescence_id, Case When fl.name Is Null Then 'Fluorescence does not exist' else '' end as fluorescence_id_error, 
tp.rapo_rate, Case When tp.rapo_rate Is Null Then 'Rapo Rate cannot be null' else '' end as rapo_rate_error,
tp.rapo_amount, Case When tp.rapo_amount Is Null Then 'Rapo Amount cannot be null' else '' end as rapo_amount_error,
tp.discount, Case When tp.discount Is Null Then 'Discount cannot be null' else '' end as discount_error,
tp.rate, Case When tp.rate Is Null Then 'Rate cannot be null' else '' end as rate_error,
tp.amount, Case When tp.amount Is Null Then 'Amount cannot be null' else '' end as amount_error,
tp.table, Case When tp.table Is Null Then 'Table cannot be null' else '' end as table_error,
tp.table_per, Case When tp.table_per Is Null Then 'Rable% cannot be null' else '' end as table_per_error,
tp.depth, Case When tp.depth Is Null Then 'Depth cannot be null' else '' end as depth_error,
tp.depth_per, Case When tp.depth_per Is Null Then 'Depth% cannot be null' else '' end as depth_per_error,
tp.measurement, Case When tp.measurement Is Null Then 'Measurement cannot be null' else '' end as measurement_error,
tp.length, Case When tp.length Is Null Then 'Length cannot be null' else '' end as length_error,
tp.width, Case When tp.width Is Null Then 'Width cannot be null' else '' end as width_error,
tp.height, Case When tp.height Is Null Then 'Height cannot be null' else '' end as height_error,
tp.ratio, Case When tp.ratio Is Null Then 'Ratio cannot be null' else '' end as ratio_error,
tp.bgm_id, Case When b.name Is Null Then 'BGM  does not exist' else '' end as bgm_id_error,
tp.city, Case When tp.city Is Null Then 'City cannot be null' else '' end as city_error,
tp.country, Case When tp.country Is Null Then 'Country cannot be null' else '' end as country_error
from 
temp_products tp 
left join products p1 ON tp.stone_id=p1.stone_id
left join products p2 ON tp.stone_id=p2.cert_no
left Join sizes as s on s.name=tp.size_id
left Join shapes as sh on sh.name=tp.shape_id
left Join colors as c on c.name=tp.color_id
left Join fancy_colors as fc on fc.name=tp.colors_id AND fc.type=0
left Join fancy_colors as fo on fo.name=tp.overtone_id AND fo.type=1
left Join fancy_colors as fi on fi.name=tp.intensity_id AND fi.type=2
left Join clarities as cl on cl.name=tp.clarity_id
left Join finishes as cu on cu.name=tp.cut_id AND cu.type=0
left Join finishes as po on po.name=tp.polish_id AND po.type=1
left Join finishes as sy on sy.name=tp.symmetry_id AND sy.type=2
left Join fluorescences as fl on fl.name=tp.fluorescence_id AND fl.type=0
left Join fluorescences as b on b.name=tp.bgm_id AND b.type=2
where tp.uuid=P_uuid order by tp.id;

END //

DELIMITER ;