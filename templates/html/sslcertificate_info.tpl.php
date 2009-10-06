<h2>Ip Zertifikat</h2>
countryName: {$certificate_data.ip.subject.C}<br>
stateOrProvinceName: {$certificate_data.ip.subject.ST}<br>
localityName: {$certificate_data.ip.subject.L}<br>
organizationName: {$certificate_data.ip.subject.O}<br>
organizationalUnitName: {$certificate_data.ip.subject.OU}<br>
commonName: {$certificate_data.ip.subject.CN}<br>
emailAddress: {$certificate_data.ip.subject.emailAddress}<br>

Erstellt am: {$certificate_data.ip.validFrom_time_t}<br>
Gültig bis: {$certificate_data.ip.validTo_time_t}<br></p>

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