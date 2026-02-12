@extends('layouts.app')

@section('title', 'Policies - Otto Investments')

@section('content')
    {{-- Hero section --}}
    <section class="bg-gradient-to-br from-pink-100 via-amber-50 to-sky-100 py-12">
        <div class="container mx-auto px-4 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">Store Policies</h1>
            <p class="text-base md:text-lg text-slate-600 max-w-2xl mx-auto">
                Clear, transparent policies to ensure you have the best shopping experience with us.
            </p>
        </div>
    </section>

    <div class="container mx-auto px-4 lg:px-8 py-10 max-w-4xl">
        <div class="space-y-8">
            {{-- Shipping Policy --}}
            <section class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
                <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="text-2xl">🚚</span>
                    Shipping & Delivery
                </h2>
                <div class="space-y-4 text-sm md:text-base text-slate-700 leading-relaxed">
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Delivery Areas</h3>
                        <p>We currently deliver to all major cities and towns across Kenya, including Nairobi, Mombasa, Kisumu, Nakuru, and surrounding areas. For remote locations, please contact us to confirm delivery availability.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Delivery Times</h3>
                        <ul class="list-disc list-inside space-y-1 ml-2">
                            <li><strong>Nairobi & Environs:</strong> 1-2 business days</li>
                            <li><strong>Major Cities:</strong> 2-4 business days</li>
                            <li><strong>Other Locations:</strong> 3-7 business days</li>
                        </ul>
                        <p class="mt-2">Orders placed before 2:00 PM on weekdays are typically processed the same day.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Shipping Fees</h3>
                        <p>Shipping costs are calculated at checkout based on your location and order size. We offer free shipping on orders over Ksh 5,000 within Nairobi.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Order Tracking</h3>
                        <p>Once your order ships, you'll receive a tracking number via email. You can track your order status in your account dashboard or by contacting us directly.</p>
                    </div>
                </div>
            </section>

            {{-- Returns & Exchanges --}}
            <section class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
                <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-undo text-2xl text-amber-600"></i>
                    Returns & Exchanges
                </h2>
                <div class="space-y-4 text-sm md:text-base text-slate-700 leading-relaxed">
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Return Policy</h3>
                        <p>We want you to be completely satisfied with your purchase. You can return unopened, unused items in their original packaging within <strong>14 days</strong> of delivery for a full refund or exchange.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Conditions for Returns</h3>
                        <ul class="list-disc list-inside space-y-1 ml-2">
                            <li>Items must be unopened and in original packaging</li>
                            <li>Original receipt or order confirmation required</li>
                            <li>Items must not be damaged or show signs of use</li>
                            <li>For safety reasons, opened toys cannot be returned unless defective</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Defective Items</h3>
                        <p>If you receive a defective or damaged item, please contact us immediately. We'll arrange a replacement or full refund at no cost to you, including return shipping.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">How to Return</h3>
                        <p>Contact us at <a href="mailto:hello@brighttoys.com" class="text-amber-600 hover:underline">hello@brighttoys.com</a> or through your account dashboard to initiate a return. We'll provide you with a return authorization and instructions.</p>
                    </div>
                </div>
            </section>

            {{-- Privacy Policy --}}
            <section class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
                <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="text-2xl">🔒</span>
                    Privacy & Data Protection
                </h2>
                <div class="space-y-4 text-sm md:text-base text-slate-700 leading-relaxed">
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Information We Collect</h3>
                        <p>We collect information necessary to process your orders and provide excellent service, including:</p>
                        <ul class="list-disc list-inside space-y-1 ml-2 mt-2">
                            <li>Name, email address, and phone number</li>
                            <li>Shipping and billing addresses</li>
                            <li>Payment information (processed securely through trusted providers)</li>
                            <li>Order history and preferences</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">How We Use Your Information</h3>
                        <p>Your information is used solely to:</p>
                        <ul class="list-disc list-inside space-y-1 ml-2 mt-2">
                            <li>Process and fulfill your orders</li>
                            <li>Communicate about your orders and account</li>
                            <li>Send you updates about new products and promotions (with your consent)</li>
                            <li>Improve our website and customer experience</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Data Security</h3>
                        <p>We use industry-standard security measures to protect your personal information. All payment transactions are encrypted and processed through secure payment gateways. We never store your full credit card details on our servers.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Your Rights</h3>
                        <p>You have the right to access, update, or delete your personal information at any time. You can also opt out of marketing communications by updating your preferences in your account or contacting us directly.</p>
                    </div>
                </div>
            </section>

            {{-- Terms & Conditions --}}
            <section class="bg-white border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
                <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <span class="text-2xl">📋</span>
                    Terms & Conditions
                </h2>
                <div class="space-y-4 text-sm md:text-base text-slate-700 leading-relaxed">
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Product Information</h3>
                        <p>We strive to provide accurate product descriptions and images. However, colors may vary slightly due to screen settings. If you receive an item that significantly differs from the description, please contact us for a return or exchange.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Pricing</h3>
                        <p>All prices are displayed in Kenyan Shillings (Ksh) and are subject to change without notice. We reserve the right to correct pricing errors, even after an order has been placed.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Age Recommendations</h3>
                        <p>Age recommendations on our products are guidelines based on safety standards and developmental appropriateness. Parents should always supervise play and use their judgment when selecting toys for their children.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-900 mb-2">Limitation of Liability</h3>
                        <p>Otto Investments is not liable for any indirect, incidental, or consequential damages arising from the use of our services. Our liability is limited as per our terms and conditions.</p>
                    </div>
                </div>
            </section>

            {{-- Contact for Questions --}}
            <div class="bg-gradient-to-br from-amber-50 to-pink-50 border border-amber-200 rounded-2xl p-6 text-center">
                <h3 class="font-bold text-slate-900 mb-2">Questions About Our Policies?</h3>
                <p class="text-sm text-slate-600 mb-4">If you have any questions or concerns about our policies, please don't hesitate to reach out.</p>
                <a href="{{ route('pages.contact') }}" 
                   class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-2.5 rounded-lg shadow-sm shadow-amber-500/30 transition-colors">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
@endsection
