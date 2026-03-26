@extends('layouts.app_inner')
@section('title', 'Privacy Policy')
@section('content')
	<!-- Start of Main -->
        <main class="main">
            <!-- Start of Breadcrumb -->
            <nav class="breadcrumb-nav">
                <div class="container">
                    <ul class="breadcrumb mb-6">
                        <li><a href="{{url('/')}}">Home</a></li>
                        <li><a href="{{url('/privacy_policy')}}">Privacy Policy</a></li>
                    </ul>
                </div>
            </nav>
            <!-- End of Breadcrumb -->

            <!-- Start of Pgae Contetn -->
            <div class="page-content mb-10 pb-2">
               
               <section class="privacy-policy-sections">
                   <div class="container">
                       <div class="row">
                           <div class="col-md-12">
                               <div class="privacy-wrap">
         <h3>1) Ondjango’s Commitment</h3>
         <p>Ondjango values privacy and the protection of personal data as a fundamental pillar in our relationship with customers, users, vendors/suppliers, partners, and employees. This Policy explains what data we collect, for which purposes, under what legal grounds, for how long, and the rights of data subjects, in accordance with <strong>Angolan Data Protection Law No. 22/11 of 17 June</strong> and the guidelines of the <strong>Data Protection Agency (APD)</strong>.</p>
         <p>When selling to consumers in the <strong>EU/UK</strong>, GDPR and local consumer protection rules also apply.</p>
      </div>
      <div class="privacy-wrap">
         <h3>2) Data Controller</h3>
         <p>The Data Controller is Ondjango (and, when applicable, its affiliates). Ondjango implements appropriate technical and organizational security measures and cooperates with the competent authorities.</p>
         <p>For institutional contact, please use the Help Center available on the Platform.</p>
      </div>
      <div class="privacy-wrap">
         <h3>3) Data Subjects</h3>
         <ul>
            <li>Customers and Users (visitors, registered users, buyers)</li>
            <li>Vendors/Suppliers (resellers, distributors, manufacturers)</li>
            <li>Candidates and Employees (recruitment and HR)</li>
            <li>Partners and Business Contacts</li>
            <li>Any individuals interacting with the Platform</li>
         </ul>
      </div>
      <div class="privacy-wrap">
         <h3>4) Categories of Personal Data</h3>
         <ul>
            <li><strong>Identification & Contact:</strong> name, ID document, address, account contact details</li>
            <li><strong>Profile/Account:</strong> username, preferences, browsing history (cookies), wishlists</li>
            <li><strong>Transactional:</strong> orders, payments (tokenized), invoices/receipts</li>
            <li><strong>Logistics:</strong> delivery addresses, tracking information, proof of delivery</li>
            <li><strong>Technical/Usage:</strong> IP, device ID, OS, authentication logs, web/app events</li>
            <li><strong>Communication:</strong> Help Center interactions; call recordings only when informed and applicable</li>
            <li><strong>Location:</strong> approximate coordinates/country (when enabled)</li>
            <li><strong>Professional (vendors):</strong> company data, settlement details, KYC/AML information</li>
            <li><strong>User-submitted content:</strong> reviews, messages, attachments (catalogues, images)</li>
         </ul>
         <p><strong>Note:</strong> Ondjango does not store full card details; payments are processed by third-party
            PSPs (e.g., EMIS, banks, PayPal/Payoneer).
         </p>
      </div>
      <div class="privacy-wrap">
         <h3>5) Legal Grounds</h3>
         <ul>
            <li><strong>Contract performance / pre-contractual steps:</strong> account management, orders, payments, deliveries, returns, support</li>
            <li><strong>Legal obligation:</strong> tax/accounting compliance, responses to authorities, AML/CFT</li>
            <li><strong>Legitimate interest:</strong> security, fraud prevention, service improvement, basic personalization, defense of rights, customer marketing (with opt-out)</li>
            <li><strong>Consent:</strong> non-essential cookies/marketing, newsletters, precise geolocation, call recording for quality purposes</li>
         </ul>
         <p><strong>Minors:</strong> Services are intended for adults. If processing minors’ data is legally required,
            parental consent is mandatory.
         </p>
      </div>
      <div class="privacy-wrap">
         <h3>6) Purposes and Retention Periods</h3>
         <ul>
            <li><strong>Commercial operations:</strong> registration, cart, checkout, billing, payments, logistics integrations (e.g., DHL, MyUS), banks/EMIS, vendors (API/Excel)</li>
            <li><strong>Support & Quality:</strong> handling requests/complaints; call recordings when notified</li>
            <li><strong>Marketing & Personalization:</strong> campaigns, coupons, communications (with opt-out)</li>
            <li><strong>Security & Fraud Prevention:</strong> authentication, logs, audit trails, KYC/AML</li>
            <li><strong>Analytics:</strong> usage metrics (favoring aggregated/anonymous data)</li>
            <li><strong>Compliance & Tax:</strong> accounting, fiscal archiving, reporting</li>
         </ul>
         <h4>Retention guidelines:</h4>
         <ul>
            <li><strong>Account/Contract:</strong> during the relationship + legally required period</li>
            <li><strong>Call recordings (if any):</strong> minimum necessary (e.g., 6–24 months), unless litigation</li>
            <li><strong>Video surveillance (if applicable):</strong> up to 30 days, unless otherwise required by law</li>
            <li><strong>Marketing:</strong> while consent/active relationship exists or internal minimum retention applies</li>
            <li><strong>Cookies/analytics:</strong> according to cookie table and browser settings</li>
         </ul>
      </div>
      <div class="privacy-wrap">
         <h3>7) Data Collection Methods</h3>
         <ul>
            <li><strong>Direct:</strong> registration, checkout, forms, Help Center</li>
            <li><strong>Automatic:</strong> cookies, pixels, SDKs, logs, analytics</li>
            <li><strong>Third-party sources:</strong> PSPs, logistics partners, identity/SSO services, integrated vendors; publicly available sources when permitted</li>
            <li><strong>Affiliates:</strong> intra-group sharing necessary for operations (e.g., settlements in foreign currency)</li>
         </ul>
      </div>
      <div class="privacy-wrap">
         <h3>8) Data Sharing (strictly necessary)</h3>
         <ul>
            <li><strong>Payments:</strong> EMIS, banks, PayPal, Payoneer</li>
            <li><strong>Logistics:</strong> carriers and freight partners (shipping, tracking, returns)</li>
            <li><strong>Vendors/Suppliers:</strong> order execution and support, stock/price synchronization</li>
            <li><strong>Technology & Security:</strong> cloud hosting, CDN, email/SMS services, anti-fraud tools, analytics</li>
            <li><strong>Consultants/Auditors:</strong> under confidentiality obligations</li>
            <li><strong>Authorities:</strong> whenever legally required</li>
         </ul>
         <p>All processors are bound by data processing agreements and security measures.</p>
      </div>
      <div class="privacy-wrap">
         <h3>9) International Data Transfers</h3>
         <p>Data may be transferred outside Angola (e.g., EU, USA). Ondjango applies appropriate safeguards, such as standard contractual clauses and additional measures. For EU/UK operations, GDPR requirements are fully observed.</p>
      </div>
      <div class="privacy-wrap">
         <h3>10) Rights of Data Subjects</h3>
         <ul>
            <li><strong>Access:</strong> to personal data and processing information</li>
            <li><strong>Rectification / Update:</strong> request correction or update of your data</li>
            <li><strong>Objection:</strong> including marketing; withdrawal of consent at any time</li>
            <li><strong>Erasure & Restriction:</strong> when applicable</li>
            <li><strong>Portability:</strong> when applicable under GDPR</li>
            <li><strong>Right to complain:</strong> with the APD or the relevant authority in your country (EU/UK)</li>
         </ul>
         <h4>How to exercise your rights:</h4>
         <p>Use the Help Center / Privacy Portal on the Platform to submit your request.</p>
         <p>Identity verification may be required.</p>
         <p>We will respond within a reasonable period under applicable law</p>
      </div>
      <div class="privacy-wrap">
         <h3>11) Cookies and Similar Technologies</h3>
         <p>We use cookies for essential functions (login/security/checkout), analytics, functionality, and marketing (with consent where required).</p>
         <p>You may manage preferences through the cookie banner and your browser settings. Blocking essential cookies may affect platform functionality.</p>
      </div>
      <div class="privacy-wrap">
         <h3>12) Information Security</h3>
         <p>We implement logical, physical, and organizational measures: encryption when applicable, firewalls, access control, logging/audit trails, backups/business continuity, security testing, and anti-fraud frameworks.</p>
         <p>Access is restricted to authorized personnel with a legitimate need.</p>
      </div>
      <div class="privacy-wrap">
         <h3>13) Children and Adolescents</h3>
         <p>The Platform is intended for adults. If we become aware of unauthorized processing of a minor’s data, we will delete it securely.</p>
      </div>
      <div class="privacy-wrap">
         <h3>14) Changes to this Policy</h3>
         <p>We may update this Policy for legal or operational reasons. The new version will be published on the Platform with prior notice when required by law.</p>
      </div>
      <div class="privacy-wrap">
         <h3>15) Data Protection Officer (if applicable)</h3>
         <p>If a DPO is appointed, requests may be submitted via the Help Center / Privacy Portal, which will ensure proper internal handling.</p>
      </div>
      <div class="privacy-wrap">
         <h3>EU/UK Appendix (when applicable)</h3>
         <ul>
            <li><strong>Consumer rights:</strong> 14-day withdrawal right, minimum 2-year conformity guarantee, clear pre-contractual information</li>
            <li><strong>Cookies/marketing:</strong> prior consent rules (ePrivacy/GDPR)</li>
            <li><strong>Dispute resolution:</strong> access to out-of-court mechanisms (e.g., EU ODR)</li>
            <li><strong>Supervisory authorities:</strong> data subjects may lodge complaints with the authority in their country</li>
         </ul>
      </div>
                           </div>
                       </div>
                   </div>
               </section>
            <!-- End of Page Content -->
			</div>
        </main>
        <!-- End of Main -->
@endsection
        