<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: kikimr/public/api/protos/ydb_s3_internal.proto

namespace GPBMetadata\Kikimr\PBPublic\Api\Protos;

class YdbS3Internal
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Kikimr\PBPublic\Api\Protos\YdbOperation::initOnce();
        \GPBMetadata\Kikimr\PBPublic\Api\Protos\YdbValue::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
.kikimr/public/api/protos/ydb_s3_internal.protoYdb.S3Internal(kikimr/public/api/protos/ydb_value.proto"�
S3ListingRequest

table_name (	#

key_prefix (2.Ydb.TypedValue
path_column_prefix (	
path_column_delimiter (	/
start_after_key_suffix (2.Ydb.TypedValue
max_keys (
columns_to_return (	9
operation_params (2.Ydb.Operations.OperationParams"A
S3ListingResponse,
	operation (2.Ydb.Operations.Operation"u
S3ListingResult\'
common_prefixes (2.Ydb.ResultSet 
contents (2.Ydb.ResultSet
key_suffix_size (B1
com.yandex.ydb.s3_internalBS3InternalProtos�bproto3'
        , true);

        static::$is_initialized = true;
    }
}

