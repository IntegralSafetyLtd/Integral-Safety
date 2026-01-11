'use client'

import React from 'react'
import { AlertTriangle, CheckCircle, Info, Lightbulb, HelpCircle, Flame, FileText, Shield, Users, Clock } from 'lucide-react'

interface TextNode {
  type: 'text'
  text: string
  bold?: boolean
  italic?: boolean
}

interface ListItemNode {
  type: 'listitem'
  children: TextNode[]
}

interface ListNode {
  type: 'list'
  listType: 'bullet' | 'number'
  children: ListItemNode[]
}

interface HeadingNode {
  type: 'heading'
  tag: 'h1' | 'h2' | 'h3' | 'h4' | 'h5' | 'h6'
  children: TextNode[]
}

interface ParagraphNode {
  type: 'paragraph'
  children: TextNode[]
}

interface BlockquoteNode {
  type: 'blockquote'
  children: TextNode[]
}

interface CalloutNode {
  type: 'callout'
  variant: 'info' | 'warning' | 'success' | 'tip'
  title?: string
  children: TextNode[]
}

interface ImagePlaceholderNode {
  type: 'image-placeholder'
  caption: string
  icon?: 'flame' | 'document' | 'shield' | 'users' | 'clock' | 'default'
}

interface KeyTakeawaysNode {
  type: 'key-takeaways'
  title?: string
  items: string[]
}

interface FAQNode {
  type: 'faq'
  items: { question: string; answer: string }[]
}

interface StatsBoxNode {
  type: 'stats-box'
  stats: { value: string; label: string }[]
}

interface DividerNode {
  type: 'divider'
}

type ContentNode =
  | TextNode
  | ListNode
  | HeadingNode
  | ParagraphNode
  | ListItemNode
  | BlockquoteNode
  | CalloutNode
  | ImagePlaceholderNode
  | KeyTakeawaysNode
  | FAQNode
  | StatsBoxNode
  | DividerNode

interface RootContent {
  root?: {
    children?: ContentNode[]
  }
}

const iconMap = {
  flame: Flame,
  document: FileText,
  shield: Shield,
  users: Users,
  clock: Clock,
  default: FileText,
}

const calloutIcons = {
  info: Info,
  warning: AlertTriangle,
  success: CheckCircle,
  tip: Lightbulb,
}

const calloutStyles = {
  info: 'bg-blue-50 border-blue-200 text-blue-800',
  warning: 'bg-amber-50 border-amber-200 text-amber-800',
  success: 'bg-green-50 border-green-200 text-green-800',
  tip: 'bg-purple-50 border-purple-200 text-purple-800',
}

const calloutIconStyles = {
  info: 'text-blue-500',
  warning: 'text-amber-500',
  success: 'text-green-500',
  tip: 'text-purple-500',
}

function renderTextNode(node: TextNode, index: number): React.ReactNode {
  let content: React.ReactNode = node.text

  if (node.bold) {
    content = <strong key={`bold-${index}`}>{content}</strong>
  }
  if (node.italic) {
    content = <em key={`italic-${index}`}>{content}</em>
  }

  return content
}

function renderNode(node: ContentNode, index: number): React.ReactNode {
  switch (node.type) {
    case 'text':
      return renderTextNode(node, index)

    case 'paragraph':
      return (
        <p key={index}>
          {node.children?.map((child, i) => renderNode(child, i))}
        </p>
      )

    case 'heading':
      const HeadingTag = node.tag || 'h2'
      return React.createElement(
        HeadingTag,
        { key: index },
        node.children?.map((child, i) => renderNode(child, i))
      )

    case 'list':
      const ListTag = node.listType === 'number' ? 'ol' : 'ul'
      return React.createElement(
        ListTag,
        { key: index },
        node.children?.map((child, i) => renderNode(child, i))
      )

    case 'listitem':
      return (
        <li key={index}>
          {node.children?.map((child, i) => renderNode(child, i))}
        </li>
      )

    case 'blockquote':
      return (
        <blockquote key={index} className="border-l-4 border-orange-500 pl-6 my-8 italic text-gray-700 text-xl leading-relaxed">
          {node.children?.map((child, i) => renderNode(child, i))}
        </blockquote>
      )

    case 'callout': {
      const CalloutIcon = calloutIcons[node.variant]
      return (
        <div key={index} className={`not-prose my-8 p-6 rounded-lg border-l-4 ${calloutStyles[node.variant]}`}>
          <div className="flex gap-4">
            <CalloutIcon className={`w-6 h-6 flex-shrink-0 mt-0.5 ${calloutIconStyles[node.variant]}`} />
            <div>
              {node.title && (
                <h4 className="font-semibold mb-2">{node.title}</h4>
              )}
              <div className="text-sm leading-relaxed">
                {node.children?.map((child, i) => renderTextNode(child as TextNode, i))}
              </div>
            </div>
          </div>
        </div>
      )
    }

    case 'image-placeholder': {
      const IconComponent = iconMap[node.icon || 'default']
      return (
        <figure key={index} className="not-prose my-10">
          <div className="relative h-64 md:h-80 rounded-xl overflow-hidden bg-gradient-to-br from-navy-700 to-navy-900 flex items-center justify-center">
            <div className="text-center text-white p-6">
              <div className="w-20 h-20 border-2 border-white/20 rounded-full flex items-center justify-center mx-auto mb-4 bg-white/10">
                <IconComponent className="w-10 h-10 text-white/70" />
              </div>
              <p className="text-sm font-medium text-white/80 max-w-xs mx-auto">
                {node.caption}
              </p>
            </div>
          </div>
          <figcaption className="text-center text-sm text-gray-500 mt-3 italic">
            {node.caption}
          </figcaption>
        </figure>
      )
    }

    case 'key-takeaways':
      return (
        <div key={index} className="not-prose my-10 bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-8 border border-orange-100">
          <div className="flex items-center gap-3 mb-5">
            <div className="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
              <Lightbulb className="w-5 h-5 text-white" />
            </div>
            <h3 className="font-heading text-xl font-semibold text-navy-900">
              {node.title || 'Key Takeaways'}
            </h3>
          </div>
          <ul className="space-y-3">
            {node.items.map((item, i) => (
              <li key={i} className="flex items-start gap-3">
                <CheckCircle className="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" />
                <span className="text-gray-700">{item}</span>
              </li>
            ))}
          </ul>
        </div>
      )

    case 'faq':
      return (
        <div key={index} className="not-prose my-12">
          <div className="flex items-center gap-3 mb-8">
            <div className="w-10 h-10 bg-navy-900 rounded-lg flex items-center justify-center">
              <HelpCircle className="w-5 h-5 text-white" />
            </div>
            <h2 className="font-heading text-2xl font-semibold text-navy-900">
              Frequently Asked Questions
            </h2>
          </div>
          <div className="space-y-6">
            {node.items.map((item, i) => (
              <div key={i} className="bg-cream rounded-xl p-6">
                <h3 className="font-heading text-lg font-semibold text-navy-900 mb-3">
                  {item.question}
                </h3>
                <p className="text-gray-600 leading-relaxed">
                  {item.answer}
                </p>
              </div>
            ))}
          </div>
        </div>
      )

    case 'stats-box':
      return (
        <div key={index} className="not-prose my-10 grid grid-cols-2 md:grid-cols-4 gap-4">
          {node.stats.map((stat, i) => (
            <div key={i} className="bg-navy-900 text-white rounded-xl p-6 text-center">
              <div className="text-3xl md:text-4xl font-bold text-orange-500 mb-2">
                {stat.value}
              </div>
              <div className="text-sm text-white/70">
                {stat.label}
              </div>
            </div>
          ))}
        </div>
      )

    case 'divider':
      return (
        <hr key={index} className="my-12 border-t-2 border-gray-100" />
      )

    default:
      return null
  }
}

export function SimpleRichText({ data }: { data: RootContent | null | undefined }) {
  if (!data?.root?.children) {
    return null
  }

  return (
    <>
      {data.root.children.map((node, index) => renderNode(node as ContentNode, index))}
    </>
  )
}
