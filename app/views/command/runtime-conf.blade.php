<?xml version="1.0" encoding="UTF-8"?>
<config>
    <propel>
        <datasources default="zidisha">
            <datasource id="zidisha">
                <adapter>pgsql</adapter>
                <connection>
                    <dsn>pgsql:host={{$databaseHost}};port={{$databasePortNumber}};dbname={{$databaseName}};user={{$databaseUsername}};password={{$databasePassword}}</dsn>
                    @if($environment == 'local')
                    <classname>Zidisha\Vendor\DebugBarPDO</classname>
                    @endif
                </connection>
            </datasource>
        </datasources>
        <log>
            <logger name="defaultLogger">
                <type>stream</type>
                <path>{{storage_path()}}/logs/propel.log</path>
                <level>300</level>
            </logger>
        </log>
    </propel>
</config>
