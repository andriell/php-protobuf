<?php

use Protobuf\GPBType;
use Protobuf\Pbf;

return array(
    'HeaderBlock' => array(
        1 => array(
            Pbf::NAME => 'bbox',
            Pbf::MESSAGE => 'HeaderBBox',
            Pbf::REPEATED => false,
        ),
        4 => array(
            Pbf::NAME => 'required_features',
            Pbf::TYPE => GPBType::STRING,
        ),
        5 => array(
            Pbf::NAME => 'optional_features',
            Pbf::TYPE => GPBType::STRING,
        ),
        16 => array(
            Pbf::NAME => 'writingprogram',
            Pbf::TYPE => GPBType::STRING,
            Pbf::REPEATED => false,
        ),
        17 => array(
            Pbf::NAME => 'source',
            Pbf::TYPE => GPBType::STRING,
            Pbf::REPEATED => false,
        ),
        32 => array(
            Pbf::NAME => 'osmosis_replication_timestamp',
            Pbf::TYPE => GPBType::INT64,
            Pbf::REPEATED => false,
        ),
        33 => array(
            Pbf::NAME => 'osmosis_replication_sequence_number',
            Pbf::TYPE => GPBType::INT64,
            Pbf::REPEATED => false,
        ),
        34 => array(
            Pbf::NAME => 'osmosis_replication_base_url',
            Pbf::TYPE => GPBType::STRING,
            Pbf::REPEATED => false,
        ),
    ),
    'HeaderBBox' => array(
        1 => array(
            Pbf::NAME => 'left',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::REPEATED => false,
        ),
        2 => array(
            Pbf::NAME => 'right',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::REPEATED => false,
        ),
        3 => array(
            Pbf::NAME => 'top',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::REPEATED => false,
        ),
        4 => array(
            Pbf::NAME => 'bottom',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::REPEATED => false,
        ),
    ),
    'PrimitiveBlock' => array(
        1 => array(
            Pbf::NAME => 'stringtable',
            Pbf::MESSAGE => 'StringTable',
            Pbf::REPEATED => false,
        ),
        2 => array(
            Pbf::NAME => 'primitivegroup',
            Pbf::MESSAGE => 'PrimitiveGroup',
        ),
        17 => array(
            Pbf::NAME => 'granularity',
            Pbf::TYPE => GPBType::INT32,
            Pbf::REPEATED => false,
        ),
        18 => array(
            Pbf::NAME => 'date_granularity',
            Pbf::TYPE => GPBType::INT32,
            Pbf::REPEATED => false,
        ),
        19 => array(
            Pbf::NAME => 'lat_offset',
            Pbf::TYPE => GPBType::INT64,
            Pbf::REPEATED => false,
        ),
        20 => array(
            Pbf::NAME => 'lon_offset',
            Pbf::TYPE => GPBType::INT64,
            Pbf::REPEATED => false,
        ),
    ),
    'PrimitiveGroup' => array(
        1 => array(
            Pbf::NAME => 'nodes',
            Pbf::MESSAGE => 'Node',
        ),
        2 => array(
            Pbf::NAME => 'dense',
            Pbf::MESSAGE => 'DenseNodes',
            Pbf::REPEATED => false,
        ),
        3 => array(
            Pbf::NAME => 'ways',
            Pbf::MESSAGE => 'Way',
        ),
        4 => array(
            Pbf::NAME => 'relations',
            Pbf::MESSAGE => 'Relation',
        ),
        5 => array(
            Pbf::NAME => 'changesets',
            Pbf::MESSAGE => 'ChangeSet',
        ),
    ),
    'StringTable' => array(
        1 => array(
            Pbf::NAME => 's',
            Pbf::TYPE => GPBType::BYTES,
        ),
    ),
    'Info' => array(
        1 => array(
            Pbf::NAME => 'version',
            Pbf::TYPE => GPBType::INT32,
            Pbf::REPEATED => false,
        ),
        2 => array(
            Pbf::NAME => 'timestamp',
            Pbf::TYPE => GPBType::INT64,
            Pbf::REPEATED => false,
        ),
        3 => array(
            Pbf::NAME => 'changeset',
            Pbf::TYPE => GPBType::INT64,
            Pbf::REPEATED => false,
        ),
        4 => array(
            Pbf::NAME => 'uid',
            Pbf::TYPE => GPBType::INT32,
            Pbf::REPEATED => false,
        ),
        5 => array(
            Pbf::NAME => 'user_sid',
            Pbf::TYPE => GPBType::UINT32,
            Pbf::REPEATED => false,
        ),
        6 => array(
            Pbf::NAME => 'visible',
            Pbf::TYPE => GPBType::BOOL,
            Pbf::REPEATED => false,
        ),
    ),
    'DenseInfo' => array(
        1 => array(
            Pbf::NAME => 'version',
            Pbf::TYPE => GPBType::INT32,
            Pbf::PACKED => true,
        ),
        2 => array(
            Pbf::NAME => 'timestamp',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::PACKED => true,
        ),
        3 => array(
            Pbf::NAME => 'changeset',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::PACKED => true,
        ),
        4 => array(
            Pbf::NAME => 'uid',
            Pbf::TYPE => GPBType::SINT32,
            Pbf::PACKED => true,
        ),
        5 => array(
            Pbf::NAME => 'user_sid',
            Pbf::TYPE => GPBType::SINT32,
            Pbf::PACKED => true,
        ),
        6 => array(
            Pbf::NAME => 'visible',
            Pbf::TYPE => GPBType::BOOL,
            Pbf::PACKED => true,
        ),
    ),
    'ChangeSet' => array(
        1 => array(
            Pbf::NAME => 'id',
            Pbf::TYPE => GPBType::INT64,
            Pbf::REPEATED => false,
        ),
    ),
    'Node' => array(
        1 => array(
            Pbf::NAME => 'id',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::REPEATED => false,
        ),
        2 => array(
            Pbf::NAME => 'keys',
            Pbf::TYPE => GPBType::UINT32,
            Pbf::PACKED => true,
        ),
        3 => array(
            Pbf::NAME => 'vals',
            Pbf::TYPE => GPBType::UINT32,
            Pbf::PACKED => true,
        ),
        4 => array(
            Pbf::NAME => 'info',
            Pbf::MESSAGE => 'Info',
            Pbf::REPEATED => false,
        ),
        8 => array(
            Pbf::NAME => 'lat',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::REPEATED => false,
        ),
        9 => array(
            Pbf::NAME => 'lon',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::REPEATED => false,
        ),
    ),
    'DenseNodes' => array(
        1 => array(
            Pbf::NAME => 'id',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::PACKED => true,
        ),
        5 => array(
            Pbf::NAME => 'denseinfo',
            Pbf::MESSAGE => 'DenseInfo',
            Pbf::REPEATED => false,
        ),
        8 => array(
            Pbf::NAME => 'lat',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::PACKED => true,
        ),
        9 => array(
            Pbf::NAME => 'lon',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::PACKED => true,
        ),
        10 => array(
            Pbf::NAME => 'keys_vals',
            Pbf::TYPE => GPBType::UINT32,
            Pbf::PACKED => true,
        ),
    ),
    'Way' => array(
        1 => array(
            Pbf::NAME => 'id',
            Pbf::TYPE => GPBType::INT64,
            Pbf::REPEATED => false,
        ),
        2 => array(
            Pbf::NAME => 'keys',
            Pbf::TYPE => GPBType::UINT64,
            Pbf::PACKED => true,
        ),
        3 => array(
            Pbf::NAME => 'vals',
            Pbf::TYPE => GPBType::UINT64,
            Pbf::PACKED => true,
        ),
        4 => array(
            Pbf::NAME => 'info',
            Pbf::MESSAGE => 'Info',
            Pbf::REPEATED => false,
        ),
        8 => array(
            Pbf::NAME => 'refs',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::PACKED => true,
        ),
    ),
    'Relation' => array(
        1 => array(
            Pbf::NAME => 'id',
            Pbf::TYPE => GPBType::INT64,
            Pbf::REPEATED => false,
        ),
        2 => array(
            Pbf::NAME => 'keys',
            Pbf::TYPE => GPBType::UINT64,
            Pbf::PACKED => true,
        ),
        3 => array(
            Pbf::NAME => 'vals',
            Pbf::TYPE => GPBType::UINT64,
            Pbf::PACKED => true,
        ),
        4 => array(
            Pbf::NAME => 'info',
            Pbf::MESSAGE => 'Info',
            Pbf::REPEATED => false,
        ),
        8 => array(
            Pbf::NAME => 'roles_sid',
            Pbf::TYPE => GPBType::INT32,
            Pbf::PACKED => true,
        ),
        9 => array(
            Pbf::NAME => 'memids',
            Pbf::TYPE => GPBType::SINT64,
            Pbf::PACKED => true,
        ),
        10 => array(
            Pbf::NAME => 'types',
            Pbf::TYPE => GPBType::ENUM,
            Pbf::PACKED => true,
        ),
    ),
    // External format
    'Blob' => array(
        1 => array(
            Pbf::NAME => 'raw',
            Pbf::TYPE => GPBType::BYTES,
            Pbf::REPEATED => false,
        ),
        2 => array(
            Pbf::NAME => 'raw_size',
            Pbf::TYPE => GPBType::INT32,
            Pbf::REPEATED => false,
        ),
        3 => array(
            Pbf::NAME => 'zlib_data',
            Pbf::TYPE => GPBType::BYTES,
            Pbf::REPEATED => false,
        ),
        4 => array(
            Pbf::NAME => 'lzma_data',
            Pbf::TYPE => GPBType::BYTES,
            Pbf::REPEATED => false,
        ),
        5 => array(
            Pbf::NAME => 'OBSOLETE_bzip2_data',
            Pbf::TYPE => GPBType::BYTES,
            Pbf::REPEATED => false,
        ),
    ),
    'BlobHeader' => array(
        1 => array(
            Pbf::NAME => 'type',
            Pbf::TYPE => GPBType::STRING,
            Pbf::REPEATED => false,
        ),
        2 => array(
            Pbf::NAME => 'indexdata',
            Pbf::TYPE => GPBType::BYTES,
            Pbf::REPEATED => false,
        ),
        3 => array(
            Pbf::NAME => 'datasize',
            Pbf::TYPE => GPBType::INT32,
            Pbf::REPEATED => false,
        ),
    )
);
