create table user_person(
    id_user serial primary key,
    username varchar (255) not null UNIQUE,
    email varchar (255) not null UNIQUE,                    
    password varchar (255) not null,
    status BOOLEAN DEFAULT false,
    isadmin BOOLEAN DEFAULT false                    
);
create table hardware(
    id_hardware serial primary key,
    name varchar (255) not null,
    type varchar (255) not null,
    description varchar (255) not null
);
create table node(
    id_node serial primary key,
    id_user integer not null,                    
    id_hardware_node integer not null, 
    id_hardware_sensor integer[10], 
    name varchar (255) not null, 
    location varchar (255) not null, 
    field_sensor text[10] not null,
    is_public BOOLEAN default false,
    foreign key (id_hardware_node) references hardware (id_hardware) on update cascade on delete cascade,
    foreign key (id_user) references user_person (id_user) on update cascade on delete cascade
);
ALTER TABLE node
    ALTER COLUMN field_sensor SET DEFAULT '{}'::text[];

CREATE OR REPLACE FUNCTION set_default_field_sensor()
    RETURNS TRIGGER AS $$
BEGIN
    NEW.field_sensor[1] := '';
    NEW.field_sensor[2] := '';
    NEW.field_sensor[3] := '';
    NEW.field_sensor[4] := '';
    NEW.field_sensor[5] := '';
    NEW.field_sensor[6] := '';
    NEW.field_sensor[7] := '';
    NEW.field_sensor[8] := '';
    NEW.field_sensor[9] := '';
    NEW.field_sensor[10] := '';
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER set_default_field_sensor_trigger
    BEFORE INSERT OR UPDATE ON node
    FOR EACH ROW
    WHEN (pg_trigger_depth() = 0)
    EXECUTE FUNCTION set_default_field_sensor();
    
create table feed(
    id_node integer not null,
    time timestamp NOT NULL,
    value float[10] NOT NULL,
    foreign key (id_node) references node (id_node) on update cascade on delete cascade                    
);