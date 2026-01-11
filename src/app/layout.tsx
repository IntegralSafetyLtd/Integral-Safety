// Root layout is a passthrough - each route group defines its own HTML structure
// This is required because Payload CMS provides its own complete HTML document

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  return children
}
