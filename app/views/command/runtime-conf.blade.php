<?xml version="1.0" encoding="UTF-8"?>
<config>
    <propel>
        <datasources default="zidisha">
            <datasource id="zidisha">
                <adapter>pgsql</adapter>
                <connection>
                    <dsn>pgsql:host={{$databaseHost}};port={{$databasePortNumber}};dbname={{$databaseName}};user={{$databaseUsername}};password={{$databasePassword}}</dsn>
                </connection>
            </datasource>
        </datasources>
    </propel>
</config>