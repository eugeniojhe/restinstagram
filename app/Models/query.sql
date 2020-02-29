SELECT u.*, COUNT(f.id_followed) AS Following, 0 AS followed FROM users u
LEFT JOIN followers  f 
ON f.id_follower = u.id 
WHERE U.id = 1
GROUP BY U.id
UNION 
SELECT u.*, 0 AS following  ,COUNT(f.id_follower) AS followed FROM users u 
LEFT JOIN followers f 
ON f.id_followed = u.id 
WHERE u.id = 1 
GROUP BY u.id; 