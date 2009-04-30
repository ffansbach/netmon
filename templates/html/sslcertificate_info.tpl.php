<h2>Node Zertifikat</h2>
countryName: {$certificate_data.node.subject.C}<br>
stateOrProvinceName: {$certificate_data.node.subject.ST}<br>
localityName: {$certificate_data.node.subject.L}<br>
organizationName: {$certificate_data.node.subject.O}<br>
organizationalUnitName: {$certificate_data.node.subject.OU}<br>
commonName: {$certificate_data.node.subject.CN}<br>
emailAddress: {$certificate_data.node.subject.emailAddress}<br>

Erstellt am: {$certificate_data.node.validFrom_time_t}<br>
Gültig bis: {$certificate_data.node.validTo_time_t}<br></p>

<h2>Server Zertifikat</h2>
countryName: {$certificate_data.subnet.subject.C}<br>
stateOrProvinceName: {$certificate_data.subnet.subject.ST}<br>
localityName: {$certificate_data.subnet.subject.L}<br>
organizationName: {$certificate_data.subnet.subject.O}<br>
organizationalUnitName: {$certificate_data.subnet.subject.OU}<br>
commonName: {$certificate_data.subnet.subject.CN}<br>
emailAddress: {$certificate_data.subnet.subject.emailAddress}<br>

Erstellt am: {$certificate_data.subnet.validFrom_time_t}<br>
Gültig bis: {$certificate_data.subnet.validTo_time_t}<br></p>

<h2>Client Config (/etc/config/openvpn)</h2>
<pre>
{$vpn_config}
</pre>