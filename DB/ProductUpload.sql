DELIMITER //

CREATE PROCEDURE ProductUpload(IN P_uuid CHAR(36))
BEGIN
-- set @P_uuid = 'cd3b0fa2-737f-423d-ba73-03c25f0bf6db'; 

insert into products (id, stone_id, cert_no, cert_type, cert_url, image, video, diamond_type, 
size_id, carat, shape_id, color_id, colors_id, overtone_id, intensity_id, clarity_id, cut_id, polish_id, 
symmetry_id, fluorescence_id, rapo_rate, rapo_amount, discount, rate, amount, `table`, table_per, depth, depth_per, measurement, length, width, height, ratio, 
bgm_id, city, country, created_at, updated_at)
select 
UUID(), tp.stone_id, tp.cert_no, 
case when tp.cert_type='IGI' then 1 when tp.cert_type='GIA' then 2 when tp.cert_type='HRD' then 3 when tp.cert_type='Delight Grading' then 4 else 0 end cert_type, 
tp.cert_url, tp.image, tp.video, case when tp.diamond_type='HPHT' then 1 when tp.diamond_type='CVD' then 2 else 0 end as diamond_type,
(select s.id from sizes s where s.name=tp.size_id and s.deleted_at is null and s.status=1 group by s.name) as size_id, 
tp.carat, 

(select sh.id from shapes sh where sh.name=tp.shape_id and sh.deleted_at is null and sh.status=1 group by sh.name) as shape_id, 
(select c.id from colors c where c.name=tp.color_id and c.deleted_at is null and c.status=1 group by c.name) as color_id, 
(select fc.id from fancy_colors fc where fc.name=tp.colors_id and fc.deleted_at is null and fc.status=1 and fc.type=0 group by fc.name) as colors_id, 
(select fo.id from fancy_colors fo where fo.name=tp.overtone_id and fo.deleted_at is null and fo.status=1 and fo.type=1 group by fo.name) as overtone_id, 
(select fi.id from fancy_colors fi where fi.name=tp.intensity_id and fi.deleted_at is null and fi.status=1 and fi.type=2 group by fi.name) as intensity_id, 
(select cl.id from clarities cl where cl.name=tp.clarity_id and cl.deleted_at is null and cl.status=1 group by cl.name) as clarity_id, 
(select cu.id from finishes cu where cu.name=tp.cut_id and cu.deleted_at is null and cu.status=1 and cu.type=0 group by cu.name) as cut_id, 
(select po.id from finishes po where po.name=tp.polish_id and po.deleted_at is null and po.status=1 and po.type=1 group by po.name) as polish_id, 
(select sy.id from finishes sy where sy.name=tp.symmetry_id and sy.deleted_at is null and sy.status=1 and sy.type=2 group by sy.name) as symmetry_id, 
(select fl.id from fluorescences fl where fl.name=tp.fluorescence_id and fl.deleted_at is null and fl.status=1 AND fl.type=0 group by fl.name) as fluorescence_id, 

tp.rapo_rate, tp.rapo_amount, tp.discount, tp.rate, tp.amount, tp.table, tp.table_per, tp.depth, tp.depth_per, tp.measurement, tp.length, tp.width, tp.height, tp.ratio, 
(select b.id from fluorescences b where b.name=tp.bgm_id and b.deleted_at is null and b.status=1 AND b.type=2 group by b.name) as bgm_id, 
tp.city, tp.country, now(), now()
from 
temp_products tp 
left join products p1 ON tp.stone_id=p1.stone_id
left join products p2 ON tp.stone_id=p2.cert_no
where tp.uuid=P_uuid and p1.stone_id is null and p2.cert_no is null 
order by tp.id;

END //

DELIMITER ;