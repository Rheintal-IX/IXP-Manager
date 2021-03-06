Hi,

You or someone in your organisation requested a LoA on the
following cross connect to {{ env( 'IDENTITY_ORGNAME' ) }}.


```
Facility:       {{ $ppp->getPatchPanel()->getCabinet()->getLocation()->getName() }}
Rack:           {{ $ppp->getPatchPanel()->getCabinet()->getCololocation() }}
Patch panel:    {{ $ppp->getPatchPanel()->getName() }}
Port:           {{ $ppp->getName() }} @if( $ppp->hasSlavePort() ) *(duplex port)* @endif

Colo Reference: {{ $ppp->getColoCircuitRef() }}
Type:           {{ $ppp->getPatchPanel()->resolveCableType() }}
Connector:      {{ $ppp->getPatchPanel()->resolveConnectorType() }}
State:          {{ $ppp->resolveStates() }}
```

Please find the LoA attached as a PDF.

If you have any queries about this, please reply to this email.

@include('patch-panel-port/emails/signature')

